@push('styles')
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
        background-color: black;
    }

    #scanner-wrapper {
        position: relative;
        width: 100vw;
        height: 100vh;
    }

    #reader {
        width: 100%;
        height: 100%;
    }

    #reader__scan_region{
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }

    #reader__dashboard{
      position: absolute;
      top: 78%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    #html5-qrcode-button-camera-start, #html5-qrcode-button-camera-stop{
      content: 'Start';
      padding: 6px 12px;
      border-radius: 25px;
      outline: none;
      border: none;
    }

    #reader__dashboard_section_csr, #html5-qrcode-anchor-scan-type-change, #reader__dashboard_section{
      color: white;
    }

    .overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 80%;
        height: 250px;
        transform: translate(-50%, -50%);
        border: 2px dashed rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(255,255,255,0.3);
        z-index: 2;
    }

    .scan-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: rgba(255, 0, 0, 0.7);
        animation: scanMove 2s infinite linear;
    }

    @keyframes scanMove {
        0% { top: 0; }
        100% { top: calc(100% - 2px); }
    }

    .result-box {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.975rem;
        min-width: 60%;
        text-align: center;
        z-index: 3;
    }
</style>
@endpush

<div id="scanner-wrapper">
    <div id="reader"></div>
    <div class="overlay">
        <div class="scan-line"></div>
    </div>
    <div class="result-box">
        <span id="barcode-result">Menunggu scan...</span>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.clear(); // Stop scanning setelah sukses

        const currentPath = window.location.pathname;
        window.location.href = `${currentPath}/${decodedText}/confirmation`;
    }


    const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
        fps: 60,
        qrbox: { width: 300, height: 300 },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true,
    });

    html5QrcodeScanner.render(onScanSuccess);
</script>

@endpush