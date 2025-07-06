<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web3 Financial Inclusion Demo - Nigeria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for Nigerian Web3 Demo */
        :root {
            --primary-green: #00b894;
            --naira-gold: #ff7675;
            --trust-blue: #0984e3;
            --warning-orange: #fdcb6e;
            --success-green: #00b894;
            --danger-red: #e17055;
            --light-bg: #f8f9fa;
            --dark-text: #2d3436;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00b894 0%, #0984e3 100%);
            min-height: 100vh;
            color: var(--dark-text);
        }

        .app-container {
            max-width: 420px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .status-bar {
            background: #000;
            color: white;
            padding: 8px 16px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-green), var(--trust-blue));
            color: white;
            padding: 20px 16px;
            text-align: center;
            position: relative;
        }

        .wallet-card {
            background: rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .balance-display {
            text-align: center;
            margin: 20px 0;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .balance-usd {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 20px 0;
        }

        .action-btn {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 16px 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            color: var(--dark-text);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .action-btn i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 8px;
            color: var(--primary-green);
        }

        .section {
            padding: 20px 16px;
            background: white;
            margin: 8px 0;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--dark-text);
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .transaction-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .transaction-icon.send {
            background: var(--naira-gold);
        }

        .transaction-icon.receive {
            background: var(--success-green);
        }

        .transaction-icon.save {
            background: var(--trust-blue);
        }

        .transaction-details h6 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--dark-text);
        }

        .transaction-details p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }

        .transaction-amount {
            font-weight: 600;
            color: var(--dark-text);
        }

        .trust-badge {
            background: linear-gradient(135deg, var(--success-green), var(--trust-blue));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 8px 0;
        }

        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 8px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-green), var(--trust-blue));
            transition: width 0.3s ease;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 420px;
            background: white;
            border-top: 1px solid #eee;
            padding: 12px 0;
            z-index: 1000;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-item {
            text-align: center;
            color: #666;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            flex: 1;
        }

        .nav-item.active {
            color: var(--primary-green);
            background: rgba(0, 184, 148, 0.1);
        }

        .nav-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .nav-item span {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 16px;
            padding: 24px;
            width: 90%;
            max-width: 350px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-text);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 20px auto;
            display: block;
            border: 2px solid #eee;
            border-radius: 8px;
        }

        .wallet-address {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9rem;
            word-break: break-all;
            margin: 12px 0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--dark-text);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--trust-blue));
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin: 16px 0;
            border: none;
        }

        .alert-success {
            background: rgba(0, 184, 148, 0.1);
            color: var(--success-green);
            border-left: 4px solid var(--success-green);
        }

        .alert-info {
            background: rgba(9, 132, 227, 0.1);
            color: var(--trust-blue);
            border-left: 4px solid var(--trust-blue);
        }

        .savings-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin: 16px 0;
        }

        .yield-display {
            text-align: center;
            margin: 16px 0;
        }

        .yield-rate {
            font-size: 2rem;
            font-weight: bold;
            color: var(--warning-orange);
        }

        .loan-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin: 16px 0;
        }

        .trust-score {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 16px 0;
        }

        .trust-stars {
            color: var(--warning-orange);
            font-size: 1.2rem;
        }

        .hidden {
            display: none !important;
        }

        .illustration {
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .step {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            margin: 0 4px;
            transition: all 0.3s ease;
        }

        .step.active {
            background: var(--primary-green);
            transform: scale(1.2);
        }

        .onboarding-screen {
            text-align: center;
            padding: 40px 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, #00b894 0%, #0984e3 100%);
            color: white;
        }

        .onboarding-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 20px 0;
        }

        .onboarding-text {
            font-size: 1.1rem;
            margin: 16px 0;
            opacity: 0.9;
            line-height: 1.6;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin: 8px 0;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }

        @media (max-width: 480px) {
            .app-container {
                max-width: 100%;
                height: 100vh;
            }
            
            .balance-amount {
                font-size: 2rem;
            }
            
            .action-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- App Container -->
    <div class="app-container">
        <!-- Status Bar -->
        <div class="status-bar">
            <span>9:41 AM</span>
            <span>🔋 87%</span>
        </div>

        <!-- Onboarding Screens -->
        <div id="onboarding" class="onboarding-screen">
            <div id="onboarding-content">
                <!-- Step 1 -->
                <div class="onboarding-step active" data-step="1">
                    <svg class="illustration" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Woman with phone at market -->
                        <circle cx="100" cy="80" r="15" fill="#8B4513" stroke="#654321" stroke-width="2"/>
                        <rect x="90" y="95" width="20" height="30" fill="#FF6B6B" rx="2"/>
                        <rect x="85" y="105" width="30" height="20" fill="#4ECDC4" rx="2"/>
                        <rect x="93" y="110" width="14" height="10" fill="#45B7D1" rx="1"/>
                        <circle cx="100" cy="115" r="2" fill="#FFD93D"/>
                        <rect x="60" y="130" width="80" height="40" fill="#8B4513" rx="4"/>
                        <circle cx="70" cy="140" r="6" fill="#FF6B6B"/>
                        <circle cx="85" cy="140" r="6" fill="#4ECDC4"/>
                        <circle cx="100" cy="140" r="6" fill="#FFD93D"/>
                        <text x="100" y="190" text-anchor="middle" fill="white" font-size="12">Market Trading</text>
                    </svg>
                    <h2 class="onboarding-title">Welcome to Web3 Money</h2>
                    <p class="onboarding-text">Send, receive, and save money safely with your phone. No bank account needed!</p>
                </div>

                <!-- Step 2 -->
                <div class="onboarding-step" data-step="2">
                    <svg class="illustration" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Farmer with crops -->
                        <circle cx="100" cy="70" r="12" fill="#8B4513"/>
                        <rect x="95" y="82" width="10" height="20" fill="#228B22" rx="1"/>
                        <rect x="90" y="92" width="20" height="15" fill="#8B4513" rx="1"/>
                        <rect x="85" y="107" width="30" height="25" fill="#4169E1" rx="2"/>
                        <circle cx="70" cy="140" r="8" fill="#228B22"/>
                        <rect x="67" y="135" width="6" height="10" fill="#8B4513"/>
                        <circle cx="130" cy="140" r="8" fill="#FFD700"/>
                        <rect x="127" y="135" width="6" height="10" fill="#8B4513"/>
                        <rect x="85" y="150" width="30" height="20" fill="#8B4513" rx="2"/>
                        <text x="100" y="190" text-anchor="middle" fill="white" font-size="12">Farming & Loans</text>
                    </svg>
                    <h2 class="onboarding-title">Get Loans Without Collateral</h2>
                    <p class="onboarding-text">Build trust in your community. Get loans for your business based on your reputation.</p>
                </div>

                <!-- Step 3 -->
                <div class="onboarding-step" data-step="3">
                    <svg class="illustration" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- Piggy bank with growth -->
                        <ellipse cx="100" cy="120" rx="35" ry="25" fill="#FFB6C1"/>
                        <circle cx="80" cy="110" r="3" fill="#000"/>
                        <ellipse cx="75" cy="115" rx="8" ry="5" fill="#FF69B4"/>
                        <rect x="95" y="95" width="10" height="8" fill="#FFB6C1"/>
                        <rect x="98" y="98" width="4" height="2" fill="#000"/>
                        <circle cx="120" cy="80" r="8" fill="#32CD32"/>
                        <rect x="118" y="75" width="4" height="10" fill="#228B22"/>
                        <circle cx="140" cy="90" r="6" fill="#FFD700"/>
                        <rect x="138" y="87" width="4" height="6" fill="#FFA500"/>
                        <circle cx="85" cy="75" r="5" fill="#FF6347"/>
                        <rect x="83" y="73" width="4" height="4" fill="#FF4500"/>
                        <text x="100" y="190" text-anchor="middle" fill="white" font-size="12">Grow Your Savings</text>
                    </svg>
                    <h2 class="onboarding-title">Grow Your Money</h2>
                    <p class="onboarding-text">Save in digital naira and watch your money grow. Earn more than traditional banks!</p>
                </div>
            </div>

            <!-- Step Indicators -->
            <div class="step-indicator">
                <div class="step active" data-step="1"></div>
                <div class="step" data-step="2"></div>
                <div class="step" data-step="3"></div>
            </div>

            <!-- Navigation Buttons -->
            <div style="margin-top: 40px;">
                <button class="btn-primary" onclick="nextOnboardingStep()">Continue</button>
                <button class="btn-secondary" onclick="skipOnboarding()">Skip Tutorial</button>
            </div>
        </div>

        <!-- Main App -->
        <div id="main-app" class="hidden">
            <!-- Header -->
            <div class="header">
                <h1 style="margin: 0; font-size: 1.3rem;">Welcome, Amina!</h1>
                <div class="wallet-card">
                    <div class="balance-display">
                        <div style="font-size: 0.9rem; opacity: 0.8;">Your Digital Wallet</div>
                        <div class="balance-amount" id="balance-amount">₦45,750</div>
                        <div class="balance-usd">≈ $28.45 USD</div>
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i> Trusted Member
                        </div>
                    </div>
                    
                    <div class="wallet-address" onclick="copyAddress()">
                        <small>Your Address:</small><br>
                        <span id="wallet-address">0x742d35Cc6634C0532925a3b8D7e9A3f45bF4672d</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="action-btn" onclick="openModal('sendModal')">
                        <i class="fas fa-paper-plane"></i>
                        Send Money
                    </button>
                    <button class="action-btn" onclick="openModal('receiveModal')">
                        <i class="fas fa-qrcode"></i>
                        Receive Money
                    </button>
                    <button class="action-btn" onclick="openModal('saveModal')">
                        <i class="fas fa-piggy-bank"></i>
                        Save Money
                    </button>
                    <button class="action-btn" onclick="openModal('loanModal')">
                        <i class="fas fa-handshake"></i>
                        Get Loan
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div id="main-content">
                <!-- Recent Transactions -->
                <div class="section">
                    <h3 class="section-title">Recent Activity</h3>
                    <div id="transactions-list">
                        <div class="transaction-item">
                            <div class="transaction-info">
                                <div class="transaction-icon receive">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="transaction-details">
                                    <h6>Received from Kemi</h6>
                                    <p>Payment for tomatoes</p>
                                </div>
                            </div>
                            <div class="transaction-amount">+₦3,500</div>
                        </div>
                        
                        <div class="transaction-item">
                            <div class="transaction-info">
                                <div class="transaction-icon send">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="transaction-details">
                                    <h6>Sent to Musa</h6>
                                    <p>School fees payment</p>
                                </div>
                            </div>
                            <div class="transaction-amount">-₦12,000</div>
                        </div>
                        
                        <div class="transaction-item">
                            <div class="transaction-info">
                                <div class="transaction-icon save">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="transaction-details">
                                    <h6>Savings Interest</h6>
                                    <p>Monthly earnings</p>
                                </div>
                            </div>
                            <div class="transaction-amount">+₦234</div>
                        </div>
                    </div>
                </div>

                <!-- Trust Score -->
                <div class="section">
                    <h3 class="section-title">Community Trust</h3>
                    <div class="trust-score">
                        <div class="trust-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span>4.2/5.0</span>
                    </div>
                    <div class="alert alert-info">
                        <strong>Great reputation!</strong> You've completed 23 transactions and repaid 5 loans on time.
                    </div>
                </div>

                <!-- Savings Overview -->
                <div class="section">
                    <h3 class="section-title">Your Savings</h3>
                    <div class="savings-card">
                        <div class="yield-display">
                            <div style="font-size: 1rem; opacity: 0.9;">Annual Yield</div>
                            <div class="yield-rate">8.5%</div>
                            <div style="font-size: 1.2rem; margin-top: 8px;">₦15,000 saved</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 65%;"></div>
                        </div>
                        <div style="text-align: center; margin-top: 12px;">
                            <small>Goal: ₦25,000 by December</small>
                        </div>
                    </div>
                </div>

                <!-- Bottom spacing for nav -->
                <div style="height: 80px;"></div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <div class="nav-items">
                <a href="#" class="nav-item active" onclick="showSection('home')">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('transactions')">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Activity</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('savings')">
                    <i class="fas fa-piggy-bank"></i>
                    <span>Savings</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('loans')">
                    <i class="fas fa-handshake"></i>
                    <span>Loans</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('profile')">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Send Money Modal -->
    <div id="sendModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Send Money</h3>
                <button class="close-btn" onclick="closeModal('sendModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">To (Phone or Address)</label>
                    <input type="text" class="form-control" placeholder="Enter phone number or scan QR" id="send-recipient">
                </div>
                <div class="form-group">
                    <label class="form-label">Amount (₦)</label>
                    <input type="number" class="form-control" placeholder="0.00" id="send-amount">
                </div>
                <div class="form-group">
                    <label class="form-label">Note (Optional)</label>
                    <input type="text" class="form-control" placeholder="What's this for?" id="send-note">
                </div>
                <button class="btn-primary" onclick="sendMoney()">Send Money</button>
            </div>
        </div>
    </div>

    <!-- Receive Money Modal -->
    <div id="receiveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Receive Money</h3>
                <button class="close-btn" onclick="closeModal('receiveModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center;">
                    <p>Show this QR code to receive payment</p>
                    <svg class="qr-code" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <!-- QR Code Pattern -->
                        <rect width="200" height="200" fill="white"/>
                        <g fill="black">
                            <!-- Corner squares -->
                            <rect x="10" y="10" width="60" height="60"/>
                            <rect x="20" y="20" width="40" height="40" fill="white"/>
                            <rect x="30" y="30" width="20" height="20" fill="black"/>
                            
                            <rect x="130" y="10" width="60" height="60"/>
                            <rect x="140" y="20" width="40" height="40" fill="white"/>
                            <rect x="150" y="30" width="20" height="20" fill="black"/>
                            
                            <rect x="10" y="130" width="60" height="60"/>
                            <rect x="20" y="140" width="40" height="40" fill="white"/>
                            <rect x="30" y="150" width="20" height="20" fill="black"/>
                            
                            <!-- Data pattern -->
                            <rect x="80" y="20" width="10" height="10"/>
                            <rect x="100" y="20" width="10" height="10"/>
                            <rect x="80" y="40" width="10" height="10"/>
                            <rect x="90" y="50" width="10" height="10"/>
                            <rect x="110" y="50" width="10" height="10"/>
                            <rect x="80" y="80" width="10" height="10"/>
                            <rect x="100" y="80" width="10" height="10"/>
                            <rect x="120" y="80" width="10" height="10"/>
                            <rect x="50" y="90" width="10" height="10"/>
                            <rect x="70" y="100" width="10" height="10"/>
                            <rect x="90" y="110" width="10" height="10"/>
                            <rect x="110" y="120" width="10" height="10"/>
                            <rect x="80" y="140" width="10" height="10"/>
                            <rect x="100" y="160" width="10" height="10"/>
                            <rect x="120" y="170" width="10" height="10"/>
                        </g>
                    </svg>
                    <div class="wallet-address">
                        0x742d35Cc6634C0532925a3b8D7e9A3f45bF4672d
                    </div>
                    <button class="btn-primary" onclick="copyAddress()">Copy Address</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Money Modal -->
    <div id="saveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Save Money</h3>
                <button class="close-btn" onclick="closeModal('saveModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="savings-card">
                    <div class="yield-display">
                        <div style="font-size: 1rem; opacity: 0.9;">Current Rate</div>
                        <div class="yield-rate">8.5%</div>
                        <div style="font-size: 0.9rem; margin-top: 8px;">Annual Percentage Yield</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount to Save (₦)</label>
                    <input type="number" class="form-control" placeholder="0.00" id="save-amount">
                </div>
                <div class="form-group">
                    <label class="form-label">Savings Goal</label>
                    <select class="form-control" id="save-goal">
                        <option value="emergency">Emergency Fund</option>
                        <option value="business">Business Expansion</option>
                        <option value="education">School Fees</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <strong>Safe & Secure:</strong> Your savings are protected by blockchain technology and earn interest daily.
                </div>
                <button class="btn-primary" onclick="saveMoney()">Start Saving</button>
            </div>
        </div>
    </div>

    <!-- Get Loan Modal -->
    <div id="loanModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Get a Loan</h3>
                <button class="close-btn" onclick="closeModal('loanModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="loan-card">
                    <div style="text-align: center;">
                        <h4>Available Credit</h4>
                        <div style="font-size: 2rem; font-weight: bold; margin: 16px 0;">₦25,000</div>
                        <div style="font-size: 0.9rem; opacity: 0.9;">Based on your trust score</div>
                    </div>
                </div>
                
                <div class="trust-score">
                    <div class="trust-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span>Trust Score: 4.2/5.0</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Loan Amount (₦)</label>
                    <input type="number" class="form-control" placeholder="0.00" max="25000" id="loan-amount">
                </div>
                <div class="form-group">
                    <label class="form-label">Purpose</label>
                    <select class="form-control" id="loan-purpose">
                        <option value="business">Business Investment</option>
                        <option value="education">Education</option>
                        <option value="emergency">Emergency</option>
                        <option value="agriculture">Farming/Agriculture</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Repayment Period</label>
                    <select class="form-control" id="loan-period">
                        <option value="1">1 Month (5% interest)</option>
                        <option value="3">3 Months (12% interest)</option>
                        <option value="6">6 Months (20% interest)</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <strong>No Collateral Required:</strong> Loans are approved based on your transaction history and community trust.
                </div>
                <button class="btn-primary" onclick="requestLoan()">Request Loan</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Success!</h3>
                <button class="close-btn" onclick="closeModal('successModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 4rem; color: var(--success-green); margin: 20px 0;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 id="success-title">Transaction Successful!</h3>
                    <p id="success-message">Your transaction has been completed.</p>
                    <button class="btn-primary" onclick="closeModal('successModal')">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Demo Functionality -->
    <script>
        // Demo state
        let currentBalance = 45750;
        let currentStep = 1;
        let currentSection = 'home';
        
        // Onboarding functionality
        function nextOnboardingStep() {
            if (currentStep < 3) {
                // Hide current step
                document.querySelector(`.onboarding-step[data-step="${currentStep}"]`).classList.remove('active');
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
                
                // Show next step
                currentStep++;
                document.querySelector(`.onboarding-step[data-step="${currentStep}"]`).classList.add('active');
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
            } else {
                // Finish onboarding
                finishOnboarding();
            }
        }
        
        function skipOnboarding() {
            finishOnboarding();
        }
        
        function finishOnboarding() {
            document.getElementById('onboarding').classList.add('hidden');
            document.getElementById('main-app').classList.remove('hidden');
        }
        
        // Modal functionality
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Navigation functionality
        function showSection(section) {
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item
            event.target.closest('.nav-item').classList.add('active');
            
            // Update content based on section
            currentSection = section;
            updateMainContent();
        }
        
        function updateMainContent() {
            const content = document.getElementById('main-content');
            
            switch(currentSection) {
                case 'home':
                    content.innerHTML = `
                        <!-- Recent Transactions -->
                        <div class="section">
                            <h3 class="section-title">Recent Activity</h3>
                            <div id="transactions-list">
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-icon receive">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                        <div class="transaction-details">
                                            <h6>Received from Kemi</h6>
                                            <p>Payment for tomatoes</p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount">+₦3,500</div>
                                </div>
                                
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-icon send">
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                        <div class="transaction-details">
                                            <h6>Sent to Musa</h6>
                                            <p>School fees payment</p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount">-₦12,000</div>
                                </div>
                                
                                <div class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="transaction-icon save">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="transaction-details">
                                            <h6>Savings Interest</h6>
                                            <p>Monthly earnings</p>
                                        </div>
                                    </div>
                                    <div class="transaction-amount">+₦234</div>
                                </div>
                            </div>
                        </div>

                        <!-- Trust Score -->
                        <div class="section">
                            <h3 class="section-title">Community Trust</h3>
                            <div class="trust-score">
                                <div class="trust-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span>4.2/5.0</span>
                            </div>
                            <div class="alert alert-info">
                                <strong>Great reputation!</strong> You've completed 23 transactions and repaid 5 loans on time.
                            </div>
                        </div>

                        <!-- Savings Overview -->
                        <div class="section">
                            <h3 class="section-title">Your Savings</h3>
                            <div class="savings-card">
                                <div class="yield-display">
                                    <div style="font-size: 1rem; opacity: 0.9;">Annual Yield</div>
                                    <div class="yield-rate">8.5%</div>
                                    <div style="font-size: 1.2rem; margin-top: 8px;">₦15,000 saved</div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%;"></div>
                                </div>
                                <div style="text-align: center; margin-top: 12px;">
                                    <small>Goal: ₦25,000 by December</small>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom spacing for nav -->
                        <div style="height: 80px;"></div>
                    `;
                    break;
                    
                case 'transactions':
                    content.innerHTML = `
                        <div class="section">
                            <h3 class="section-title">All Transactions</h3>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon receive">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Received from Kemi</h6>
                                        <p>Today, 2:30 PM</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">+₦3,500</div>
                            </div>
                            
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon send">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Sent to Musa</h6>
                                        <p>Yesterday, 11:15 AM</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">-₦12,000</div>
                            </div>
                            
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon save">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Savings Interest</h6>
                                        <p>July 1, 2025</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">+₦234</div>
                            </div>
                            
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon receive">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Received from Ade</h6>
                                        <p>June 28, 2025</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">+₦8,250</div>
                            </div>
                            
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon send">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Sent to Fatima</h6>
                                        <p>June 25, 2025</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">-₦5,000</div>
                            </div>
                            
                            <div style="height: 80px;"></div>
                        </div>
                    `;
                    break;
                    
                case 'savings':
                    content.innerHTML = `
                        <div class="section">
                            <h3 class="section-title">Your Savings</h3>
                            <div class="savings-card">
                                <div class="yield-display">
                                    <div style="font-size: 1rem; opacity: 0.9;">Total Saved</div>
                                    <div style="font-size: 2.5rem; font-weight: bold; margin: 16px 0;">₦15,000</div>
                                    <div style="font-size: 1rem; opacity: 0.9;">Earning 8.5% annually</div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%;"></div>
                                </div>
                                <div style="text-align: center; margin-top: 12px;">
                                    <small>Goal: ₦25,000 by December</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <h3 class="section-title">Savings Growth</h3>
                            <div class="alert alert-success">
                                <strong>Great progress!</strong> You've earned ₦1,234 in interest this year.
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin: 16px 0;">
                                <h6 style="margin: 0 0 8px 0;">Monthly Breakdown</h6>
                                <div style="display: flex; justify-content: space-between; margin: 4px 0;">
                                    <span>July 2025</span>
                                    <span>+₦234</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin: 4px 0;">
                                    <span>June 2025</span>
                                    <span>+₦198</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin: 4px 0;">
                                    <span>May 2025</span>
                                    <span>+₦156</span>
                                </div>
                            </div>
                            
                            <button class="btn-primary" onclick="openModal('saveModal')">Add More Savings</button>
                            <div style="height: 80px;"></div>
                        </div>
                    `;
                    break;
                    
                case 'loans':
                    content.innerHTML = `
                        <div class="section">
                            <h3 class="section-title">Your Loans</h3>
                            <div class="loan-card">
                                <div style="text-align: center;">
                                    <h4>Available Credit</h4>
                                    <div style="font-size: 2rem; font-weight: bold; margin: 16px 0;">₦25,000</div>
                                    <div style="font-size: 0.9rem; opacity: 0.9;">Based on your trust score</div>
                                </div>
                            </div>
                            
                            <div class="trust-score">
                                <div class="trust-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span>Trust Score: 4.2/5.0</span>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>Perfect record!</strong> You've repaid 5 loans on time, building strong community trust.
                            </div>
                            
                            <h5>Loan History</h5>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon" style="background: var(--success-green);">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Business Loan</h6>
                                        <p>Repaid on time - June 2025</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">₦10,000</div>
                            </div>
                            
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <div class="transaction-icon" style="background: var(--success-green);">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="transaction-details">
                                        <h6>Emergency Loan</h6>
                                        <p>Repaid early - March 2025</p>
                                    </div>
                                </div>
                                <div class="transaction-amount">₦5,000</div>
                            </div>
                            
                            <button class="btn-primary" onclick="openModal('loanModal')">Request New Loan</button>
                            <div style="height: 80px;"></div>
                        </div>
                    `;
                    break;
                    
                case 'profile':
                    content.innerHTML = `
                        <div class="section">
                            <h3 class="section-title">Profile</h3>
                            <div style="text-align: center; margin: 20px 0;">
                                <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white;">
                                    A
                                </div>
                                <h4>Amina Bello</h4>
                                <p>Market Trader, Lagos</p>
                            </div>
                            
                            <div class="trust-score">
                                <div class="trust-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span>Community Trust: 4.2/5.0</span>
                            </div>
                            
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin: 16px 0;">
                                <h6 style="margin: 0 0 12px 0;">Account Stats</h6>
                                <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                                    <span>Total Transactions</span>
                                    <span><strong>23</strong></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                                    <span>Loans Repaid</span>
                                    <span><strong>5/5</strong></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                                    <span>Savings Goal Progress</span>
                                    <span><strong>65%</strong></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin: 8px 0;">
                                    <span>Member Since</span>
                                    <span><strong>Jan 2025</strong></span>
                                </div>
                            </div>
                            
                            <div class="alert alert-success">
                                <strong>Verified Account</strong><br>
                                Phone number and identity verified
                            </div>
                            
                            <div style="height: 80px;"></div>
                        </div>
                    `;
                    break;
            }
        }
        
        // Transaction functions
        function sendMoney() {
            const recipient = document.getElementById('send-recipient').value;
            const amount = parseFloat(document.getElementById('send-amount').value);
            const note = document.getElementById('send-note').value;
            
            if (!recipient || !amount) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (amount > currentBalance) {
                alert('Insufficient balance');
                return;
            }
            
            // Simulate transaction
            currentBalance -= amount;
            updateBalance();
            
            // Close send modal
            closeModal('sendModal');
            
            // Show success
            showSuccess('Money Sent!', `₦${amount.toLocaleString()} sent to ${recipient}`);
            
            // Add to transaction history
            addTransaction('send', recipient, amount, note);
            
            // Clear form
            document.getElementById('send-recipient').value = '';
            document.getElementById('send-amount').value = '';
            document.getElementById('send-note').value = '';
        }
        
        function saveMoney() {
            const amount = parseFloat(document.getElementById('save-amount').value);
            const goal = document.getElementById('save-goal').value;
            
            if (!amount) {
                alert('Please enter an amount to save');
                return;
            }
            
            if (amount > currentBalance) {
                alert('Insufficient balance');
                return;
            }
            
            // Simulate savings
            currentBalance -= amount;
            updateBalance();
            
            // Close save modal
            closeModal('saveModal');
            
            // Show success
            showSuccess('Money Saved!', `₦${amount.toLocaleString()} added to your savings`);
            
            // Add to transaction history
            addTransaction('save', 'Savings Account', amount, `Goal: ${goal}`);
            
            // Clear form
            document.getElementById('save-amount').value = '';
        }
        
        function requestLoan() {
            const amount = parseFloat(document.getElementById('loan-amount').value);
            const purpose = document.getElementById('loan-purpose').value;
            const period = document.getElementById('loan-period').value;
            
            if (!amount || !purpose || !period) {
                alert('Please fill in all fields');
                return;
            }
            
            if (amount > 25000) {
                alert('Loan amount exceeds your credit limit');
                return;
            }
            
            // Simulate loan approval
            currentBalance += amount;
            updateBalance();
            
            // Close loan modal
            closeModal('loanModal');
            
            // Show success
            showSuccess('Loan Approved!', `₦${amount.toLocaleString()} has been added to your wallet`);
            
            // Add to transaction history
            addTransaction('receive', 'Loan', amount, `Purpose: ${purpose}`);
            
            // Clear form
            document.getElementById('loan-amount').value = '';
        }
        
        function updateBalance() {
            document.getElementById('balance-amount').textContent = `₦${currentBalance.toLocaleString()}`;
        }
        
        function addTransaction(type, description, amount, note) {
            // This would normally update the backend
            // For demo, we'll just log it
            console.log(`Transaction: ${type}, ${description}, ₦${amount}, ${note}`);
        }
        
        function showSuccess(title, message) {
            document.getElementById('success-title').textContent = title;
            document.getElementById('success-message').textContent = message;
            openModal('successModal');
        }
        
        function copyAddress() {
            const address = document.getElementById('wallet-address').textContent;
            navigator.clipboard.writeText(address).then(() => {
                alert('Address copied to clipboard!');
            }).catch(() => {
                alert('Could not copy address');
            });
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            // Start with onboarding
            console.log('Web3 Financial Inclusion Demo - Nigeria loaded');
        });
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>