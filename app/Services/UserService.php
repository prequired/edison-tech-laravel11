<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\Models\TwoFactorAuthentication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Service class for handling user-related business logic.
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Create a new UserService instance.
     */
    public function __construct(
        private readonly User $userModel,
    ) {}

    /**
     * Create a new user.
     *
     * @param CreateUserDTO $dto
     * @return User
     * @throws \Throwable
     */
    public function create(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'company_id' => $dto->company_id,
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
                'role' => $dto->role,
                'phone' => $dto->phone,
                'avatar' => $dto->avatar,
                'is_active' => $dto->is_active,
                'timezone' => $dto->timezone,
                'preferences' => $dto->preferences,
            ]);

            return $user->fresh(['company']);
        });
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     * @throws \Throwable
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Hash password if it's being updated
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return $user->fresh();
        });
    }

    /**
     * Activate two-factor authentication for a user.
     *
     * @param User $user
     * @param string $secret
     * @param array<int, string> $recoveryCodes
     * @return TwoFactorAuthentication
     * @throws \Throwable
     */
    public function activateTwoFactor(User $user, string $secret, array $recoveryCodes): TwoFactorAuthentication
    {
        return DB::transaction(function () use ($user, $secret, $recoveryCodes) {
            // Delete existing 2FA record if exists
            $user->twoFactorAuthentication()?->delete();

            $twoFactor = TwoFactorAuthentication::create([
                'user_id' => $user->id,
                'secret' => encrypt($secret),
                'recovery_codes' => encrypt(json_encode($recoveryCodes)),
                'enabled' => true,
                'enabled_at' => now(),
            ]);

            return $twoFactor;
        });
    }

    /**
     * Deactivate two-factor authentication for a user.
     *
     * @param User $user
     * @return bool
     * @throws \Throwable
     */
    public function deactivateTwoFactor(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $twoFactor = $user->twoFactorAuthentication;

            if (!$twoFactor) {
                throw new \RuntimeException('Two-factor authentication is not enabled for this user');
            }

            $twoFactor->update([
                'enabled' => false,
            ]);

            return true;
        });
    }

    /**
     * Verify a two-factor authentication code.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactorCode(User $user, string $code): bool
    {
        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->enabled) {
            return false;
        }

        $secret = decrypt($twoFactor->secret);

        // Use Google2FA or similar package to verify the code
        // Example: return Google2FA::verifyKey($secret, $code);

        // For now, we'll return a placeholder
        // TODO: Implement actual 2FA verification logic
        return true;
    }

    /**
     * Update user's last login timestamp.
     *
     * @param User $user
     * @return User
     */
    public function updateLastLogin(User $user): User
    {
        $user->update([
            'last_login_at' => now(),
        ]);

        return $user->fresh();
    }

    /**
     * Deactivate a user.
     *
     * @param User $user
     * @return User
     * @throws \Throwable
     */
    public function deactivate(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->update([
                'is_active' => false,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Activate a user.
     *
     * @param User $user
     * @return User
     * @throws \Throwable
     */
    public function activate(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->update([
                'is_active' => true,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Get users by company.
     *
     * @param int $companyId
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompanyUsers(int $companyId, bool $activeOnly = true): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('company_id', $companyId);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get users by role.
     *
     * @param string $role
     * @param int|null $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $role, ?int $companyId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('role', $role)
            ->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->orderBy('name')->get();
    }
}
