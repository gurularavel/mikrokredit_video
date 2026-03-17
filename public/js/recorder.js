(function () {
    const app = document.getElementById('record-app');
    if (!app) return;

    const DURATION = parseInt(app.dataset.duration, 10) || 15;
    const UPLOAD_URL = app.dataset.uploadUrl;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    const liveVideo = document.getElementById('live-video');
    const previewVideo = document.getElementById('preview-video');
    const timerNumber = document.getElementById('timer-number');
    const timerRingProgress = document.getElementById('timer-ring-progress');
    const cameraContainer = document.getElementById('camera-container');
    const previewContainer = document.getElementById('preview-container');
    const uploadOverlay = document.getElementById('upload-overlay');
    const statusMsg = document.getElementById('status-msg');
    const confirmBtn = document.getElementById('confirm-btn');
    const rerecordBtn = document.getElementById('rerecord-btn');
    const startScreen = document.getElementById('start-screen');
    const startBtn = document.getElementById('start-btn');

    const CIRCUMFERENCE = 2 * Math.PI * 54; // r=54
    timerRingProgress.style.strokeDasharray = CIRCUMFERENCE;
    timerRingProgress.style.strokeDashoffset = 0;

    let mediaStream = null;
    let mediaRecorder = null;
    let chunks = [];
    let recordedBlob = null;
    let countdownInterval = null;
    let timeLeft = DURATION;

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

    function startCountdown() {
        timeLeft = DURATION;
        timerNumber.textContent = timeLeft;
        timerRingProgress.style.strokeDashoffset = 0;

        countdownInterval = setInterval(() => {
            timeLeft--;
            timerNumber.textContent = timeLeft;
            const offset = CIRCUMFERENCE * (1 - timeLeft / DURATION);
            timerRingProgress.style.strokeDashoffset = offset;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                }
            }
        }, 1000);
    }

    function startRecording(stream) {
        mediaStream = stream;
        liveVideo.srcObject = stream;
        chunks = [];

        const mimeType = getMimeType();
        const options = mimeType ? { mimeType } : {};
        mediaRecorder = new MediaRecorder(stream, options);

        mediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) chunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            const mimeUsed = mediaRecorder.mimeType || 'video/webm';
            recordedBlob = new Blob(chunks, { type: mimeUsed });
            const url = URL.createObjectURL(recordedBlob);
            previewVideo.src = url;
            cameraContainer.style.display = 'none';
            previewContainer.style.display = 'block';
        };

        mediaRecorder.start(1000);
        startCountdown();
    }

    function stopStream() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
            mediaStream = null;
        }
    }

    async function initCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            startScreen.style.display = 'none';
            cameraContainer.style.display = 'block';
            startRecording(stream);
        } catch (err) {
            statusMsg.textContent = 'Kameraya giriş icazəsi verilmədi: ' + err.message;
            startBtn.disabled = false;
            startBtn.textContent = '▶ Yenidən cəhd et';
        }
    }

    startBtn.addEventListener('click', () => {
        startBtn.disabled = true;
        startBtn.textContent = 'Kamera açılır...';
        initCamera();
    });

    rerecordBtn.addEventListener('click', () => {
        previewContainer.style.display = 'none';
        cameraContainer.style.display = 'block';
        if (previewVideo.src) {
            URL.revokeObjectURL(previewVideo.src);
            previewVideo.src = '';
        }
        recordedBlob = null;
        initCamera();
    });

    confirmBtn.addEventListener('click', async () => {
        if (!recordedBlob) return;

        uploadOverlay.style.display = 'flex';
        statusMsg.textContent = '';
        confirmBtn.disabled = true;
        rerecordBtn.disabled = true;

        const formData = new FormData();
        const ext = recordedBlob.type.includes('mp4') ? 'mp4' : 'webm';
        formData.append('video', recordedBlob, 'recording.' + ext);
        formData.append('mime_type', recordedBlob.type);
        formData.append('_token', CSRF_TOKEN);

        try {
            const response = await fetch(UPLOAD_URL, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                stopStream();
                window.location.href = data.redirect;
            } else {
                uploadOverlay.style.display = 'none';
                statusMsg.textContent = data.message || 'Xəta baş verdi. Yenidən cəhd edin.';
                confirmBtn.disabled = false;
                rerecordBtn.disabled = false;
            }
        } catch (err) {
            uploadOverlay.style.display = 'none';
            statusMsg.textContent = 'Şəbəkə xətası: ' + err.message;
            confirmBtn.disabled = false;
            rerecordBtn.disabled = false;
        }
    });

})();
