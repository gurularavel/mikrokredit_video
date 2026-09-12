(function () {
    const app = document.getElementById('record-app');
    if (!app) return;

    const DURATION   = parseInt(app.dataset.duration, 10) || 20;
    const UPLOAD_URL = app.dataset.uploadUrl;
    const CSRF_META  = document.querySelector('meta[name="csrf-token"]');
    const CSRF_TOKEN = CSRF_META ? CSRF_META.content : '';
    const EARLY_SHOW = 10; // saniyə qaldıqda düymələri göstər

    const liveVideo          = document.getElementById('live-video');
    const previewVideo       = document.getElementById('preview-video');
    const timerNumber        = document.getElementById('timer-number');
    const timerRingProgress  = document.getElementById('timer-ring-progress');
    const cameraContainer    = document.getElementById('camera-container');
    const previewContainer   = document.getElementById('preview-container');
    const uploadOverlay      = document.getElementById('upload-overlay');
    const statusMsg          = document.getElementById('status-msg');
    const confirmBtn         = document.getElementById('confirm-btn');
    const rerecordBtn        = document.getElementById('rerecord-btn');
    const startScreen        = document.getElementById('start-screen');
    const startBtn           = document.getElementById('start-btn');
    const earlyActions       = document.getElementById('early-actions');
    const earlyConfirmBtn    = document.getElementById('early-confirm-btn');
    const earlyRerecordBtn   = document.getElementById('early-rerecord-btn');
    const teleprompterInner  = document.getElementById('teleprompter-inner');

    const CIRCUMFERENCE = 2 * Math.PI * 54;
    timerRingProgress.style.strokeDasharray  = CIRCUMFERENCE;
    timerRingProgress.style.strokeDashoffset = 0;

    let mediaStream      = null;
    let mediaRecorder    = null;
    let chunks           = [];
    let recordedBlob     = null;
    let countdownInterval = null;
    let timeLeft         = DURATION;
    let autoSend         = false;
    let rafId            = null;

    // ── Suflyör sürüşməsi ──────────────────────────────────────────────────
    function startTeleprompter() {
        if (!teleprompterInner) return;
        const innerH    = teleprompterInner.scrollHeight;
        const totalMs   = 15 * 1000; // 15 saniyəyə uygun sürət
        const startTime = performance.now();

        function step(now) {
            const elapsed  = now - startTime;
            const progress = Math.min(elapsed / totalMs, 1);
            teleprompterInner.style.transform = `translateY(-${progress * innerH}px)`;
            if (progress < 1) {
                rafId = requestAnimationFrame(step);
            }
        }
        rafId = requestAnimationFrame(step);
    }

    function stopTeleprompter() {
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function resetTeleprompter() {
        stopTeleprompter();
        if (teleprompterInner) {
            teleprompterInner.style.transform = 'translateY(0)';
        }
    }

    // ── MIME type ──────────────────────────────────────────────────────────
    function getMimeType() {
        const types = [
            'video/webm;codecs=vp8,opus',
            'video/webm;codecs=vp9,opus',
            'video/webm',
            'video/mp4',
        ];
        for (const type of types) {
            if (MediaRecorder.isTypeSupported(type)) return type;
        }
        return '';
    }

    // ── Geri sayım ────────────────────────────────────────────────────────
    function startCountdown() {
        timeLeft = DURATION;
        timerNumber.textContent = timeLeft;
        timerRingProgress.style.strokeDashoffset = 0;

        countdownInterval = setInterval(() => {
            timeLeft--;
            timerNumber.textContent = timeLeft;
            const offset = CIRCUMFERENCE * (1 - timeLeft / DURATION);
            timerRingProgress.style.strokeDashoffset = offset;

            if (timeLeft <= EARLY_SHOW && earlyActions.style.display === 'none') {
                earlyActions.style.display = 'flex';
            }

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                }
            }
        }, 1000);
    }

    // ── Yükləmə ───────────────────────────────────────────────────────────
    async function uploadBlob() {
        uploadOverlay.style.display = 'flex';
        statusMsg.textContent = '';

        const formData = new FormData();
        const ext = recordedBlob.type.includes('mp4') ? 'mp4' : 'webm';
        formData.append('video', recordedBlob, 'recording.' + ext);
        formData.append('mime_type', recordedBlob.type);
        formData.append('_token', CSRF_TOKEN);

        try {
            const response = await fetch(UPLOAD_URL, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
            });
            const data = await response.json().catch(() => null);

            if (!response.ok) {
                uploadOverlay.style.display = 'none';

                // 419 — sessiya/CSRF problemi (iframe içində cookie bloklana bilər).
                if (response.status === 419) {
                    statusMsg.textContent = 'Sessiya vaxtı bitdi. Səhifəni yeniləyib yenidən cəhd edin.';
                    confirmBtn.disabled  = false;
                    rerecordBtn.disabled = false;
                    return;
                }

                // Qalıcı token xətaları (link tapılmadı / vaxtı bitib / istifadə olunub):
                // retry heç vaxt uğurlu olmayacaq — buna görə AVTOMATİK təkrar YOX,
                // düymələri də aktivləşdirmirik ki, əl ilə də təkrar göndərilməsin.
                if (data && data.retryable === false) {
                    statusMsg.textContent = data.message || 'Link etibarsızdır. Yeni link tələb edin.';
                    confirmBtn.disabled  = true;
                    rerecordBtn.disabled = true;
                    return;
                }

                // Digər xətalar (server/validation/şəbəkə) — istifadəçi yenidən çəkə/göndərə bilər.
                statusMsg.textContent = (data && data.message)
                    ? data.message
                    : ('Server xətası: HTTP ' + response.status);
                confirmBtn.disabled   = false;
                rerecordBtn.disabled  = false;
                return;
            }

            if (data && data.success) {
                stopStream();
                window.location.href = data.redirect;
            } else {
                uploadOverlay.style.display = 'none';
                statusMsg.textContent = (data && data.message) || 'Xəta baş verdi. Yenidən cəhd edin.';
                confirmBtn.disabled   = false;
                rerecordBtn.disabled  = false;
            }
        } catch (err) {
            uploadOverlay.style.display = 'none';
            statusMsg.textContent = 'Şəbəkə xətası: ' + err.message + ' (fayl: ' + Math.round(recordedBlob.size / 1024) + ' KB)';
            confirmBtn.disabled   = false;
            rerecordBtn.disabled  = false;
        }
    }

    // ── Qeydiyyat ─────────────────────────────────────────────────────────
    function startRecording(stream) {
        mediaStream = stream;
        liveVideo.srcObject = stream;
        chunks = [];
        autoSend = false;
        earlyActions.style.display = 'none';

        const mimeType = getMimeType();
        const recorderOpts = mimeType
            ? { mimeType, videoBitsPerSecond: 500_000 }
            : { videoBitsPerSecond: 500_000 };
        mediaRecorder = new MediaRecorder(stream, recorderOpts);

        mediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) chunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            clearInterval(countdownInterval);
            stopTeleprompter();
            const mimeUsed = mediaRecorder.mimeType || 'video/webm';
            recordedBlob = new Blob(chunks, { type: mimeUsed });

            if (autoSend) {
                uploadBlob();
                return;
            }

            const url = URL.createObjectURL(recordedBlob);
            previewVideo.src = url;
            earlyActions.style.display    = 'none';
            cameraContainer.style.display = 'none';
            previewContainer.style.display = 'block';
        };

        mediaRecorder.start(1000);
        startCountdown();
        // teleprompter is static — no scroll needed
    }

    function stopStream() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
            mediaStream = null;
        }
    }

    function stopRecording() {
        clearInterval(countdownInterval);
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
    }

    async function initCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, frameRate: { ideal: 15 } },
                audio: true,
            });
            startScreen.style.display  = 'none';
            liveVideo.style.display    = 'block';
            startRecording(stream);
        } catch (err) {
            statusMsg.textContent = 'Kameraya giriş icazəsi verilmədi: ' + err.message;
            startBtn.disabled = false;
            startBtn.textContent = '▶ Yenidən cəhd et';
        }
    }

    // ── Hadisələr ─────────────────────────────────────────────────────────
    startBtn.addEventListener('click', () => {
        startBtn.disabled    = true;
        startBtn.textContent = 'Kamera açılır...';
        cameraContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        initCamera();
    });

    // Erkən "Göndər"
    earlyConfirmBtn.addEventListener('click', () => {
        autoSend = true;
        earlyActions.style.display = 'none';
        stopRecording();
    });

    // Erkən "Yenidən çək"
    earlyRerecordBtn.addEventListener('click', () => {
        stopRecording();
        stopStream();
        resetTeleprompter();
        earlyActions.style.display = 'none';
        liveVideo.style.display    = 'none';
        startScreen.style.display  = 'block';
        startBtn.disabled    = false;
        startBtn.textContent = '▶ Başla';
    });

    // Preview "Yenidən çək"
    rerecordBtn.addEventListener('click', () => {
        previewContainer.style.display = 'none';
        if (previewVideo.src) {
            URL.revokeObjectURL(previewVideo.src);
            previewVideo.src = '';
        }
        recordedBlob = null;
        resetTeleprompter();
        cameraContainer.style.display = 'block';
        liveVideo.style.display       = 'none';
        initCamera();
    });

    // Preview "Göndər"
    confirmBtn.addEventListener('click', () => {
        if (!recordedBlob) return;
        confirmBtn.disabled  = true;
        rerecordBtn.disabled = true;
        uploadBlob();
    });

})();
