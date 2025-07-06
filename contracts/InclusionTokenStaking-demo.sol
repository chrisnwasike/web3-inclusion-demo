// SPDX-License-Identifier: MIT
pragma solidity ^0.8.19;

import "./InclusionToken-demo.sol";

/**
 * @title INCL Staking Contract
 * @dev Simple staking contract with fixed rewards for demo
 * @notice Users can stake INCL tokens and earn 5% yield after 30 seconds
 */
contract INCLStaking {
    InclusionToken public token;
    address public owner;
    
    // Staking parameters
    uint256 public stakingPeriod = 30 seconds; // Demo: 30 seconds
    uint256 public rewardRate = 5; // 5% reward
    uint256 public constant REWARD_DENOMINATOR = 100;
    
    // Stake structure
    struct Stake {
        uint256 amount;
        uint256 timestamp;
        bool withdrawn;
    }
    
    // User stakes
    mapping(address => Stake[]) public userStakes;
    mapping(address => uint256) public totalStaked;
    
    // Global stats
    uint256 public totalStakedAmount;
    uint256 public totalRewardsPaid;
    
    // Events
    event Staked(address indexed user, uint256 amount, uint256 stakeIndex, uint256 timestamp);
    event Withdrawn(address indexed user, uint256 stakeIndex, uint256 amount, uint256 reward);
    event RewardRateUpdated(uint256 newRate);
    
    modifier onlyOwner() {
        require(msg.sender == owner, "Not the owner");
        _;
    }
    
    constructor(address _tokenAddress) {
        token = InclusionToken(_tokenAddress);
        owner = msg.sender;
    }
    
    /**
     * @dev Stake INCL tokens
     */
    function stake(uint256 _amount) external {
        require(_amount > 0, "Cannot stake 0 tokens");
        require(token.balanceOf(msg.sender) >= _amount, "Insufficient balance");
        
        // Transfer tokens to contract
        token.transferFrom(msg.sender, address(this), _amount);
        
        // Create stake record
        userStakes[msg.sender].push(Stake({
            amount: _amount,
            timestamp: block.timestamp,
            withdrawn: false
        }));
        
        // Update totals
        totalStaked[msg.sender] += _amount;
        totalStakedAmount += _amount;
        
        uint256 stakeIndex = userStakes[msg.sender].length - 1;
        emit Staked(msg.sender, _amount, stakeIndex, block.timestamp);
    }
    
    /**
     * @dev Withdraw stake with rewards
     */
    function withdraw(uint256 _stakeIndex) external {
        require(_stakeIndex < userStakes[msg.sender].length, "Invalid stake index");
        
        Stake storage userStake = userStakes[msg.sender][_stakeIndex];
        require(!userStake.withdrawn, "Stake already withdrawn");
        require(block.timestamp >= userStake.timestamp + stakingPeriod, "Staking period not completed");
        
        // Calculate reward
        uint256 reward = (userStake.amount * rewardRate) / REWARD_DENOMINATOR;
        // uint256 totalAmount = userStake.amount + reward;
        
        // Mark as withdrawn
        userStake.withdrawn = true;
        
        // Update totals
        totalStaked[msg.sender] -= userStake.amount;
        totalStakedAmount -= userStake.amount;
        totalRewardsPaid += reward;
        
        // Mint reward tokens
        token.mint(msg.sender, reward);
        
        // Transfer original stake back
        token.transfer(msg.sender, userStake.amount);
        
        emit Withdrawn(msg.sender, _stakeIndex, userStake.amount, reward);
    }
    
    /**
     * @dev Get user's stake count
     */
    function getUserStakeCount(address _user) external view returns (uint256) {
        return userStakes[_user].length;
    }
    
    /**
     * @dev Get specific stake details
     */
    function getUserStake(address _user, uint256 _stakeIndex) external view returns (
        uint256 amount,
        uint256 timestamp,
        bool withdrawn,
        bool canWithdraw,
        uint256 reward,
        uint256 timeLeft
    ) {
        require(_stakeIndex < userStakes[_user].length, "Invalid stake index");
        
        Stake storage userStake = userStakes[_user][_stakeIndex];
        amount = userStake.amount;
        timestamp = userStake.timestamp;
        withdrawn = userStake.withdrawn;
        canWithdraw = !withdrawn && (block.timestamp >= timestamp + stakingPeriod);
        reward = (amount * rewardRate) / REWARD_DENOMINATOR;
        
        if (canWithdraw || withdrawn) {
            timeLeft = 0;
        } else {
            timeLeft = (timestamp + stakingPeriod) - block.timestamp;
        }
    }
    
    /**
     * @dev Get user's active stakes
     */
    function getUserActiveStakes(address _user) external view returns (
        uint256[] memory stakeIndexes,
        uint256[] memory amounts,
        uint256[] memory timestamps,
        bool[] memory canWithdrawList
    ) {
        uint256 stakeCount = userStakes[_user].length;
        uint256 activeCount = 0;
        
        // Count active stakes
        for (uint256 i = 0; i < stakeCount; i++) {
            if (!userStakes[_user][i].withdrawn) {
                activeCount++;
            }
        }
        
        // Create arrays
        stakeIndexes = new uint256[](activeCount);
        amounts = new uint256[](activeCount);
        timestamps = new uint256[](activeCount);
        canWithdrawList = new bool[](activeCount);
        
        // Fill arrays
        uint256 index = 0;
        for (uint256 i = 0; i < stakeCount; i++) {
            if (!userStakes[_user][i].withdrawn) {
                stakeIndexes[index] = i;
                amounts[index] = userStakes[_user][i].amount;
                timestamps[index] = userStakes[_user][i].timestamp;
                canWithdrawList[index] = block.timestamp >= userStakes[_user][i].timestamp + stakingPeriod;
                index++;
            }
        }
    }
    
    /**
     * @dev Update reward rate (owner only)
     */
    function updateRewardRate(uint256 _newRate) external onlyOwner {
        require(_newRate <= 50, "Reward rate too high"); // Max 50%
        rewardRate = _newRate;
        emit RewardRateUpdated(_newRate);
    }
    
    /**
     * @dev Update staking period (owner only)
     */
    function updateStakingPeriod(uint256 _newPeriod) external onlyOwner {
        stakingPeriod = _newPeriod;
    }
    
    /**
     * @dev Get contract stats
     */
    function getContractStats() external view returns (
        uint256 totalStakedTokens,
        uint256 totalRewards,
        uint256 currentRewardRate,
        uint256 currentStakingPeriod
    ) {
        totalStakedTokens = totalStakedAmount;
        totalRewards = totalRewardsPaid;
        currentRewardRate = rewardRate;
        currentStakingPeriod = stakingPeriod;
    }
}