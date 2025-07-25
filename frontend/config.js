/**
 * INCL Finance - Configuration File
 * Centralized configuration for the Web3 financial inclusion app
 */

// Network configurations
const NETWORKS = {
    sepolia: {
        chainId: '0xaa36a7', // 11155111 in hex
        chainName: 'Sepolia Test Network',
        rpcUrls: ['https://rpc.sepolia.org'],
        blockExplorerUrls: ['https://sepolia.etherscan.io'],
        nativeCurrency: {
            name: 'Sepolia ETH',
            symbol: 'ETH',
            decimals: 18
        }
    },
    mumbai: {
        chainId: '0x13881', // 80001 in hex
        chainName: 'Mumbai Testnet',
        rpcUrls: ['https://rpc-mumbai.maticvigil.com'],
        blockExplorerUrls: ['https://mumbai.polygonscan.com'],
        nativeCurrency: {
            name: 'MATIC',
            symbol: 'MATIC',
            decimals: 18
        }
    }
};

// Default network (update this to match your deployment)
const DEFAULT_NETWORK = 'sepolia';

// Contract addresses - UPDATE THESE AFTER DEPLOYMENT
const CONTRACT_ADDRESSES = {
    sepolia: {
        INCL_TOKEN: '0x...',
        FAUCET: '0x...',
        STAKING: '0x...',
        MICROLOAN: '0x...'
    },
    mumbai: {
        INCL_TOKEN: '0x...',
        FAUCET: '0x...',
        STAKING: '0x...',
        MICROLOAN: '0x...'
    }
};

// App configuration
const APP_CONFIG = {
    name: 'INCL Finance',
    version: '1.0.0',
    description: 'Web3 Financial Inclusion Platform Demo for Nigeria',
    
    // Token configuration
    token: {
        name: 'Inclusion Token',
        symbol: 'INCL',
        decimals: 18,
        faucetAmount: '100', // Amount to claim from faucet
        faucetCooldown: 3600 // 1 hour in seconds
    },
    
    // Staking configuration
    staking: {
        rewardRate: 5, // 5% reward
        stakingPeriod: 30, // 30 seconds for demo
        minStakeAmount: '1' // Minimum 1 INCL to stake
    },
    
    // Loan configuration
    microloan: {
        baseLoanAmount: '200', // Base loan amount in INCL
        interestRate: 5, // 5% interest
        loanPeriod: 300, // 5 minutes for demo
        minCreditScore: 1 // Minimum credit score to qualify
    },
    
    // UI configuration
    ui: {
        refreshInterval: 30000, // 30 seconds
        transactionTimeout: 300000, // 5 minutes
        maxTransactionHistory: 50,
        
        // Toast notification settings
        toastDuration: 5000, // 5 seconds
        
        // Animation settings
        animationDuration: 300
    },
    
    // Analytics configuration
    analytics: {
        enabled: true,
        trackUserActions: true,
        trackTransactions: true,
        apiEndpoint: 'api.php'
    },
    
    // Feature flags
    features: {
        enableStaking: true,
        enableMicroloans: true,
        enableAnalytics: true,
        enableLeaderboard: true,
        enableFeedback: true,
        enableMultiLanguage: false // Future enhancement
    },
    
    // Supported languages (for future enhancement)
    languages: {
        en: 'English',
        yo: 'Yoruba',
        ha: 'Hausa',
        ig: 'Igbo'
    }
};

// Error messages
const ERROR_MESSAGES = {
    WALLET_NOT_CONNECTED: 'Please connect your MetaMask wallet first',
    INSUFFICIENT_BALANCE: 'Insufficient INCL token balance',
    INVALID_ADDRESS: 'Please enter a valid wallet address',
    INVALID_AMOUNT: 'Please enter a valid amount',
    TRANSACTION_FAILED: 'Transaction failed. Please try again',
    NETWORK_ERROR: 'Network error. Please check your connection',
    CONTRACT_ERROR: 'Smart contract error. Please try again later',
    METAMASK_NOT_FOUND: 'MetaMask not found. Please install MetaMask',
    WRONG_NETWORK: 'Please switch to the correct network',
    USER_REJECTED: 'Transaction was rejected by user',
    INSUFFICIENT_GAS: 'Insufficient gas for transaction',
    FAUCET_COOLDOWN: 'Faucet is on cooldown. Please wait before claiming again',
    LOAN_EXISTS: 'You already have an active loan',
    CREDIT_SCORE_LOW: 'Your credit score is too low for a loan',
    STAKE_NOT_READY: 'Staking period not completed yet'
};

// Success messages
const SUCCESS_MESSAGES = {
    WALLET_CONNECTED: 'Wallet connected successfully!',
    TOKENS_CLAIMED: 'Tokens claimed successfully!',
    PAYMENT_SENT: 'Payment sent successfully!',
    TOKENS_STAKED: 'Tokens staked successfully!',
    STAKE_WITHDRAWN: 'Stake withdrawn with rewards!',
    LOAN_APPROVED: 'Loan approved and transferred!',
    LOAN_REPAID: 'Loan repaid successfully!',
    FEEDBACK_SAVED: 'Thank you for your feedback!'
};

// Gas limit estimates for transactions
const GAS_LIMITS = {
    TOKEN_TRANSFER: 50000,
    TOKEN_APPROVE: 50000,
    FAUCET_CLAIM: 100000,
    STAKE_TOKENS: 150000,
    WITHDRAW_STAKE: 100000,
    REQUEST_LOAN: 200000,
    REPAY_LOAN: 150000
};

// Utility functions
const CONFIG_UTILS = {
    /**
     * Get current network configuration
     */
    getCurrentNetwork: () => {
        return NETWORKS[DEFAULT_NETWORK];
    },
    
    /**
     * Get contract addresses for current network
     */
    getContractAddresses: () => {
        return CONTRACT_ADDRESSES[DEFAULT_NETWORK];
    },
    
    /**
     * Check if a feature is enabled
     */
    isFeatureEnabled: (feature) => {
        return APP_CONFIG.features[feature] || false;
    },
    
    /**
     * Get gas limit for transaction type
     */
    getGasLimit: (txType) => {
        return GAS_LIMITS[txType] || 100000;
    },
    
    /**
     * Format token amount for display
     */
    formatTokenAmount: (amount, decimals = 2) => {
        return parseFloat(amount).toFixed(decimals) + ' ' + APP_CONFIG.token.symbol;
    },
    
    /**
     * Validate wallet address
     */
    isValidAddress: (address) => {
        return /^0x[a-fA-F0-9]{40}$/.test(address);
    },
    
    /**
     * Shorten wallet address for display
     */
    shortenAddress: (address) => {
        if (!address) return '';
        return address.substring(0, 6) + '...' + address.substring(38);
    },
    
    /**
     * Get block explorer URL for transaction
     */
    getExplorerUrl: (txHash) => {
        const network = CONFIG_UTILS.getCurrentNetwork();
        return network.blockExplorerUrls[0] + '/tx/' + txHash;
    },
    
    /**
     * Get faucet links for current network
     */
    getFaucetLinks: () => {
        const faucets = {
            sepolia: [
                'https://sepoliafaucet.com/',
                'https://faucet.sepolia.dev/'
            ],
            baseSepolia: [
                'https://www.coinbase.com/faucets/base-ethereum-sepolia-faucet'
            ],
            mumbai: [
                'https://faucet.polygon.technology/'
            ]
        };
        return faucets[DEFAULT_NETWORK] || [];
    }
};

// Export configuration for use in other files
if (typeof module !== 'undefined' && module.exports) {
    // Node.js environment
    module.exports = {
        NETWORKS,
        DEFAULT_NETWORK,
        CONTRACT_ADDRESSES,
        APP_CONFIG,
        ERROR_MESSAGES,
        SUCCESS_MESSAGES,
        GAS_LIMITS,
        CONFIG_UTILS
    };
} else {
    // Browser environment
    window.INCL_CONFIG = {
        NETWORKS,
        DEFAULT_NETWORK,
        CONTRACT_ADDRESSES,
        APP_CONFIG,
        ERROR_MESSAGES,
        SUCCESS_MESSAGES,
        GAS_LIMITS,
        CONFIG_UTILS
    };
}

// Development helpers
const DEV_HELPERS = {
    /**
     * Log configuration for debugging
     */
    logConfig: () => {
        console.log('INCL Finance Configuration:', {
            network: DEFAULT_NETWORK,
            contracts: CONFIG_UTILS.getContractAddresses(),
            features: APP_CONFIG.features
        });
    },
    
    /**
     * Validate configuration
     */
    validateConfig: () => {
        const addresses = CONFIG_UTILS.getContractAddresses();
        const issues = [];
        
        Object.entries(addresses).forEach(([contract, address]) => {
            if (address === '0x...') {
                issues.push(`${contract} address not configured`);
            }
        });
        
        if (issues.length > 0) {
            console.warn('Configuration Issues:', issues);
            return false;
        }
        
        console.log('Configuration is valid!');
        return true;
    },
    
    /**
     * Get deployment checklist
     */
    getDeploymentChecklist: () => {
        return [
            'Deploy INCL Token contract',
            'Deploy Faucet contract with token address',
            'Deploy Staking contract with token address',
            'Deploy Microloan contract with token address',
            'Add minter permissions for all contracts',
            'Update contract addresses in config.js',
            'Test all contract functions',
            'Deploy frontend to web server',
            'Test complete user flow'
        ];
    }
};

// Add dev helpers to exports
if (typeof window !== 'undefined') {
    window.INCL_CONFIG.DEV_HELPERS = DEV_HELPERS;
}