@extends('layouts.app')
@section('title', 'Attendance QR Scanner | EZ Fitness')
@section('header', 'Attendance QR')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4 py-md-6">
    <div class="max-w-[1800px] mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-4 p-md-6 mb-4 mb-md-6">
            <div class="flex justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-1">QR Code Scanner</h1>
                    <p class="text-sm text-gray-600">Scan member QR codes for attendance check-in/check-out</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs text-gray-600">Today's Date</p>
                    <p class="text-sm sm:text-base font-semibold text-gray-800">{{ now()->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Mobile Tab Navigation (visible only on small screens) -->
        <div class="flex lg:hidden mb-4 bg-white rounded-lg shadow-sm p-1 gap-1">
            <button id="tab-scanner" onclick="switchTab('scanner')"
                class="flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 bg-blue-600 text-white">
                📷 Scanner
            </button>
            <button id="tab-attendance" onclick="switchTab('attendance')"
                class="flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100">
                📋 Attendance
            </button>
        </div>

        <div class="grid lg:grid-cols-12 gap-4 gap-md-6">

            <!-- ═══════════════════════════════════════════════════════════
                 SCANNER PANEL (Left – 4 cols on desktop, full-width mobile)
            ════════════════════════════════════════════════════════════════ -->
            <div id="panel-scanner" class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm p-4 p-md-6 lg:sticky lg:top-4">
                    <h3 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Camera Scanner</h3>

                    <!-- QR Reader -->
                    <div class="relative mb-4">
                        <div id="qr-reader" style="width:100%;"></div>
                        <div id="scanner-status" class="mt-3 text-center text-sm text-gray-600 font-medium">
                            Initializing camera…
                        </div>
                    </div>

                    <!-- Camera Controls -->
                    <div class="flex gap-3 mb-4 mb-md-6">
                        <button id="start-scan"
                            class="flex-1 bg-gray-600 text-white px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg hover:bg-blue-700 transition font-medium text-sm sm:text-base">
                            Start Scanning
                        </button>
                        <button id="stop-scan"
                            class="flex-1 bg-red-600 text-white px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg hover:bg-red-700 transition font-medium text-sm sm:text-base"
                            disabled>
                            Stop Scanning
                        </button>
                    </div>

                    <!-- Result Display -->
                    <div class="border-t pt-4">
                        <h4 class="text-base sm:text-lg font-semibold mb-3 text-gray-800">Scan Result</h4>
                        <div id="result-container" class="bg-gray-50 rounded-lg p-4 sm:p-6 min-h-[220px] sm:min-h-[280px]">
                            <div class="text-center text-gray-400">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                <p class="text-sm">Scan a QR code to see member details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 ATTENDANCE TABLE (Right – 8 cols on desktop, full-width mobile)
            ════════════════════════════════════════════════════════════════ -->
            <div id="panel-attendance" class="lg:col-span-8 hidden lg:block">
                <div class="bg-white rounded-lg shadow-sm">

                    <!-- Table Header -->
                    <div class="p-4 p-md-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div>
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Today's Attendance</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Real-time check-in and check-out logs</p>
                            </div>
                            <!-- Stats counters -->
                            <div class="flex gap-2 sm:gap-3">
                                <div class="flex-1 sm:flex-none text-center bg-green-50 px-3 py-2 sm:px-4 rounded-lg">
                                    <p class="text-xs text-green-600 font-medium whitespace-nowrap">Checked In</p>
                                    <p id="checkin-count" class="text-xl sm:text-2xl font-bold text-green-700">0</p>
                                </div>
                                <div class="flex-1 sm:flex-none text-center bg-blue-50 px-3 py-2 sm:px-4 rounded-lg">
                                    <p class="text-xs text-blue-600 font-medium whitespace-nowrap">Checked Out</p>
                                    <p id="checkout-count" class="text-xl sm:text-2xl font-bold text-blue-700">0</p>
                                </div>
                                <div class="flex-1 sm:flex-none text-center bg-gray-50 px-3 py-2 sm:px-4 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium">Total</p>
                                    <p id="total-count" class="text-xl sm:text-2xl font-bold text-gray-700">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Desktop table (hidden on mobile, shown on md+) ── -->
                    <div class="hidden md:block overflow-x-auto max-h-[600px] overflow-y-auto" id="table-scroll-container">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="attendance-table-body" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p>No attendance records yet today</p>
                                        <p class="text-sm text-gray-400 mt-1">Scan QR codes to start logging attendance</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ── Mobile card list (shown on mobile/sm, hidden on md+) ── -->
                    <div class="md:hidden" id="attendance-cards-container">
                        <div id="attendance-cards-body" class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto">
                            <div class="px-4 py-10 text-center text-gray-500">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm">No attendance records yet today</p>
                                <p class="text-xs text-gray-400 mt-1">Scan QR codes to start logging</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode;
let isScanning = false;
let currentTab = 'scanner'; // mobile tab state

// ─── Cooldown tracking ───────────────────────────────────────────────────────
const COOLDOWN_MS = 60 * 1000;
const scanCooldowns = {};
let cooldownTimerInterval = null;

function getRemainingCooldown(qrData) {
    const last = scanCooldowns[qrData];
    if (!last) return 0;
    const elapsed = Date.now() - last;
    return elapsed < COOLDOWN_MS ? COOLDOWN_MS - elapsed : 0;
}

function recordScan(qrData) {
    scanCooldowns[qrData] = Date.now();
}

// ─── Mobile tab switching ────────────────────────────────────────────────────
function switchTab(tab) {
    currentTab = tab;
    const scannerPanel = document.getElementById('panel-scanner');
    const attendancePanel = document.getElementById('panel-attendance');
    const tabScanner = document.getElementById('tab-scanner');
    const tabAttendance = document.getElementById('tab-attendance');

    if (tab === 'scanner') {
        scannerPanel.classList.remove('hidden');
        attendancePanel.classList.add('hidden', 'lg:block');
        attendancePanel.classList.remove('block');
        tabScanner.className = 'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 bg-blue-600 text-white';
        tabAttendance.className = 'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100';
    } else {
        scannerPanel.classList.add('hidden');
        scannerPanel.classList.remove('lg:col-span-4'); // don't hide on desktop via class
        attendancePanel.classList.remove('hidden');
        attendancePanel.classList.add('block');
        tabScanner.className = 'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 text-gray-600 hover:bg-gray-100';
        tabAttendance.className = 'flex-1 py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 bg-blue-600 text-white';
        loadTodayAttendance();
    }
}

// Ensure desktop always shows both panels regardless of tab state
function applyDesktopLayout() {
    if (window.innerWidth >= 1024) {
        document.getElementById('panel-scanner').classList.remove('hidden');
        document.getElementById('panel-attendance').classList.remove('hidden');
        document.getElementById('panel-attendance').classList.add('lg:block');
    }
}

window.addEventListener('resize', applyDesktopLayout);

// ─── Main init ───────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const startBtn  = document.getElementById('start-scan');
    const stopBtn   = document.getElementById('stop-scan');
    const statusDiv = document.getElementById('scanner-status');
    const resultContainer = document.getElementById('result-container');

    html5QrCode = new Html5Qrcode("qr-reader");

    loadTodayAttendance();
    setInterval(loadTodayAttendance, 10000);

    // ── Start scanning ───────────────────────────────────────────────────────
    startBtn.addEventListener('click', async function () {
        try {
            const config = {
                fps: 10,
                qrbox: { width: 220, height: 220 },
                aspectRatio: 1.0
            };
            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            );
            isScanning = true;
            startBtn.disabled = true;
            stopBtn.disabled  = false;
            statusDiv.textContent = 'Scanning… Point camera at QR code';
            statusDiv.className   = 'mt-3 text-center text-sm text-green-600 font-semibold';
        } catch (err) {
            console.error('Scanner error:', err);
            statusDiv.textContent = 'Error: ' + err;
            statusDiv.className   = 'mt-3 text-center text-sm text-red-600';
        }
    });

    // ── Stop scanning ────────────────────────────────────────────────────────
    stopBtn.addEventListener('click', async function () {
        if (isScanning) {
            try {
                await html5QrCode.stop();
                isScanning = false;
                startBtn.disabled = false;
                stopBtn.disabled  = true;
                statusDiv.textContent = 'Scanner stopped';
                statusDiv.className   = 'mt-3 text-center text-sm text-gray-600';
                clearCooldownTimer();
            } catch (err) {
                console.error('Stop error:', err);
            }
        }
    });

    // ── Scan success handler ─────────────────────────────────────────────────
    function onScanSuccess(decodedText, decodedResult) {
        const remaining = getRemainingCooldown(decodedText);
        if (remaining > 0) {
            showCooldownWarning(remaining, decodedText);
            return;
        }

        html5QrCode.pause();
        clearCooldownTimer();

        statusDiv.textContent = 'Processing…';
        statusDiv.className   = 'mt-3 text-center text-sm text-yellow-600 font-semibold';

        recordScan(decodedText);

        fetch('{{ route('attendance.scan') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_data: decodedText })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                data.action === 'checkin' ? showCheckInSuccess(data) : showCheckOutSuccess(data);
                loadTodayAttendance();
                // On mobile, briefly flash the attendance tab badge
                flashAttendanceBadge();
            } else {
                showError(data.message);
                delete scanCooldowns[decodedText];
            }
            resumeAfterDelay(3000);
        })
        .catch(error => {
            showError('Network error: ' + error.message);
            delete scanCooldowns[decodedText];
            resumeAfterDelay(3000);
        });
    }

    function onScanFailure(error) { /* ignore */ }

    // ── Resume after delay ───────────────────────────────────────────────────
    function resumeAfterDelay(ms) {
        setTimeout(() => {
            if (isScanning) {
                html5QrCode.resume();
                statusDiv.textContent = 'Scanning… Point camera at QR code';
                statusDiv.className   = 'mt-3 text-center text-sm text-green-600 font-semibold';
            }
        }, ms);
    }

    // ── Cooldown UI ──────────────────────────────────────────────────────────
    function showCooldownWarning(remainingMs, qrData) {
        clearCooldownTimer();
        const updateUI = () => {
            const ms = getRemainingCooldown(qrData);
            if (ms <= 0) {
                clearCooldownTimer();
                if (isScanning) {
                    statusDiv.textContent = 'Scanning… Point camera at QR code';
                    statusDiv.className   = 'mt-3 text-center text-sm text-green-600 font-semibold';
                }
                resultContainer.innerHTML = defaultResultHTML();
                return;
            }
            const secondsLeft  = Math.ceil(ms / 1000);
            const percentage   = (ms / COOLDOWN_MS) * 100;
            statusDiv.textContent = `Cooldown: ${secondsLeft}s remaining`;
            statusDiv.className   = 'mt-3 text-center text-sm text-orange-600 font-semibold';
            resultContainer.innerHTML = `
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3 border-4 border-orange-400">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg sm:text-xl font-bold text-orange-600 mb-1">Cooldown Active</h4>
                    <p class="text-xs sm:text-sm text-gray-500 mb-3">This member must wait before scanning again.</p>
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-3">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#fed7aa" stroke-width="3"/>
                            <circle id="cooldown-ring" cx="18" cy="18" r="15.9" fill="none" stroke="#f97316" stroke-width="3"
                                stroke-dasharray="${percentage.toFixed(1)} 100" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl sm:text-2xl font-bold text-orange-600">${secondsLeft}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Scanner is ready for other members</p>
                </div>
            `;
        };
        updateUI();
        cooldownTimerInterval = setInterval(updateUI, 1000);
    }

    function clearCooldownTimer() {
        if (cooldownTimerInterval !== null) {
            clearInterval(cooldownTimerInterval);
            cooldownTimerInterval = null;
        }
    }

    // ── Success / error displays ─────────────────────────────────────────────
    function showCheckInSuccess(data) {
        clearCooldownTimer();
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h4 class="text-xl sm:text-2xl font-bold text-green-600 mb-2">✓ Checked In</h4>
                <div class="bg-white rounded-lg p-3 sm:p-4 mt-3 text-left border border-green-200">
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Name:</strong> ${data.member.name}</p>
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Plan:</strong> ${data.member.plan}</p>
                    <p class="text-gray-700 text-sm sm:text-base"><strong>Time:</strong> ${data.member.check_in_time}</p>
                </div>
            </div>
        `;
        playBeep();
    }

    function showCheckOutSuccess(data) {
        clearCooldownTimer();
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h4 class="text-xl sm:text-2xl font-bold text-blue-600 mb-2">✓ Checked Out</h4>
                <div class="bg-white rounded-lg p-3 sm:p-4 mt-3 text-left border border-blue-200">
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Name:</strong> ${data.member.name}</p>
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Plan:</strong> ${data.member.plan}</p>
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Check-in:</strong> ${data.member.check_in_time}</p>
                    <p class="text-gray-700 mb-1.5 text-sm sm:text-base"><strong>Check-out:</strong> ${data.member.check_out_time}</p>
                    <p class="text-gray-700 text-sm sm:text-base"><strong>Duration:</strong> ${data.member.duration}</p>
                </div>
            </div>
        `;
        playBeep();
    }

    function showError(message) {
        clearCooldownTimer();
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h4 class="text-xl sm:text-2xl font-bold text-red-600 mb-2">Failed</h4>
                <p class="text-gray-700 mt-3 text-xs sm:text-sm">${message}</p>
            </div>
        `;
    }

    function defaultResultHTML() {
        return `
            <div class="text-center text-gray-400">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <p class="text-sm">Scan a QR code to see member details</p>
            </div>
        `;
    }

    // ── Flash attendance tab badge on mobile after successful scan ────────────
    function flashAttendanceBadge() {
        const tabAttendance = document.getElementById('tab-attendance');
        if (!tabAttendance || window.innerWidth >= 1024) return;
        tabAttendance.classList.add('ring-2', 'ring-green-400', 'ring-offset-1');
        setTimeout(() => tabAttendance.classList.remove('ring-2', 'ring-green-400', 'ring-offset-1'), 3000);
    }

    // ── Load today's attendance ──────────────────────────────────────────────
    function loadTodayAttendance() {
        fetch('{{ route('attendance.today') }}')
            .then(r => r.json())
            .then(data => {
                const tableBody  = document.getElementById('attendance-table-body');
                const cardsBody  = document.getElementById('attendance-cards-body');
                const totalCount   = document.getElementById('total-count');
                const checkinCount = document.getElementById('checkin-count');
                const checkoutCount = document.getElementById('checkout-count');

                if (data.length === 0) {
                    const emptyTable = `
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>No attendance records yet today</p>
                            <p class="text-sm text-gray-400 mt-1">Scan QR codes to start logging attendance</p>
                        </td></tr>`;
                    const emptyCards = `
                        <div class="px-4 py-10 text-center text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm">No attendance records yet today</p>
                            <p class="text-xs text-gray-400 mt-1">Scan QR codes to start logging</p>
                        </div>`;
                    tableBody.innerHTML = emptyTable;
                    cardsBody.innerHTML = emptyCards;
                    totalCount.textContent   = '0';
                    checkinCount.textContent  = '0';
                    checkoutCount.textContent = '0';
                    return;
                }

                const checkedInCount  = data.filter(a => a.status === 'checked_in').length;
                const checkedOutCount = data.filter(a => a.status === 'checked_out').length;
                totalCount.textContent    = data.length;
                checkinCount.textContent  = checkedInCount;
                checkoutCount.textContent = checkedOutCount;

                // ── Desktop rows ─────────────────────────────────────────────
                tableBody.innerHTML = data.map((attendance, index) => {
                    const isCheckedOut = attendance.status === 'checked_out';
                    const color        = isCheckedOut ? 'blue' : 'green';
                    const statusText   = isCheckedOut ? 'Checked Out' : 'Checked In';
                    const rowBg        = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    return `
                        <tr class="${rowBg} hover:bg-gray-100 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9 bg-gradient-to-br from-${color}-500 to-${color}-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                        ${attendance.name.split(' ').map(n => n[0]).join('').substring(0, 2)}
                                    </div>
                                    <p class="ml-3 text-sm font-medium text-gray-900">${attendance.name}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    ${attendance.plan}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                    </svg>
                                    ${attendance.check_in_time}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                ${attendance.check_out_time
                                    ? `<div class="flex items-center text-gray-900">
                                           <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                           </svg>
                                           ${attendance.check_out_time}
                                       </div>`
                                    : `<span class="text-gray-400">—</span>`}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                ${attendance.duration
                                    ? `<span class="font-medium text-gray-700">${attendance.duration}</span>`
                                    : `<span class="text-gray-400">In progress</span>`}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800">
                                    ${statusText}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');

                // ── Mobile cards ─────────────────────────────────────────────
                cardsBody.innerHTML = data.map((attendance) => {
                    const isCheckedOut = attendance.status === 'checked_out';
                    const color        = isCheckedOut ? 'blue' : 'green';
                    const statusText   = isCheckedOut ? 'Checked Out' : 'Checked In';
                    const initials     = attendance.name.split(' ').map(n => n[0]).join('').substring(0, 2);
                    return `
                        <div class="px-4 py-3 flex items-start gap-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-${color}-500 to-${color}-600 rounded-full flex items-center justify-center text-white font-bold text-sm mt-0.5">
                                ${initials}
                            </div>
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-gray-900 truncate">${attendance.name}</p>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800 whitespace-nowrap">${statusText}</span>
                                </div>
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 text-xs font-medium rounded bg-purple-100 text-purple-700">${attendance.plan}</span>
                                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500">
                                    <span>
                                        <span class="font-medium text-gray-700">In:</span> ${attendance.check_in_time}
                                    </span>
                                    ${attendance.check_out_time
                                        ? `<span><span class="font-medium text-gray-700">Out:</span> ${attendance.check_out_time}</span>`
                                        : ''}
                                    ${attendance.duration
                                        ? `<span><span class="font-medium text-gray-700">Duration:</span> ${attendance.duration}</span>`
                                        : `<span class="text-gray-400">In progress</span>`}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            })
            .catch(error => console.error('Error loading attendance:', error));
    }

    // ── Beep ─────────────────────────────────────────────────────────────────
    function playBeep() {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 800;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    }

    // Auto-start scanner
    setTimeout(() => startBtn.click(), 500);
});
</script>

<style>
/* ── QR Reader ─────────────────────────────────────────────────────────── */
#qr-reader {
    border: 4px solid #3b82f6;
    border-radius: 12px;
    overflow: hidden;
}
#qr-reader video {
    width: 100% !important;
    height: auto !important;
    display: block;
    transform: scaleX(-1) !important;
}
#qr-reader__dashboard { display: none !important; }
#qr-reader__scan_region { border: 2px solid #3b82f6 !important; }

/* ── Desktop table scrollbar ───────────────────────────────────────────── */
#table-scroll-container::-webkit-scrollbar { width: 6px; }
#table-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
#table-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
#table-scroll-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* ── Mobile cards scrollbar ────────────────────────────────────────────── */
#attendance-cards-body::-webkit-scrollbar { width: 4px; }
#attendance-cards-body::-webkit-scrollbar-track { background: #f8fafc; }
#attendance-cards-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

/* ── Row transition ────────────────────────────────────────────────────── */
tr { transition: background-color 0.2s ease; }

/* ── Keep panels visible on desktop regardless of mobile tab state ──────── */
@media (min-width: 1024px) {
    #panel-scanner,
    #panel-attendance {
        display: block !important;
    }
}
</style>
@endsection