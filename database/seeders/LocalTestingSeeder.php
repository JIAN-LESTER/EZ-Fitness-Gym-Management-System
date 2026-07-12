<?php

namespace Database\Seeders;

use App\Models\Branches;
use App\Models\MembershipPlan;
use App\Models\Product;
use App\Models\Subscriptions;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LocalTestingSeeder extends Seeder
{
    /**
     * Seed realistic local-only data for development and manual QA.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $branches = Branches::query()->orderBy('branch_id')->pluck('branch_id')->all();

            if ($branches === []) {
                $this->command?->warn('Skipping local testing seed: no branches found.');

                return;
            }

            $this->seedMembers($branches);
            $this->seedCarts();
            $this->seedSalesAndTransactions();
            $this->seedLogs();
        });
    }

    /**
     * @param  array<int, int>  $branches
     */
    private function seedMembers(array $branches): void
    {
        $memberRows = [
            [
                'first_name' => 'Alyssa',
                'last_name' => 'Santos',
                'username' => 'local_active_member',
                'email' => 'local.member.active@example.test',
                'branch_id' => $branches[0],
                'sex' => 'female',
                'birthday' => '1998-04-12',
                'height' => 162,
                'weight' => 58,
                'mobile_number' => '09170000001',
                'plan_name' => 'Monthly Membership',
                'subscription_name' => 'Monthly Subscription',
                'profile_status' => 'active',
                'subscription_status' => 'active',
                'approved' => true,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addDays(20),
            ],
            [
                'first_name' => 'Marco',
                'last_name' => 'Reyes',
                'username' => 'local_pending_member',
                'email' => 'local.member.pending@example.test',
                'branch_id' => $branches[1] ?? $branches[0],
                'sex' => 'male',
                'birthday' => '1995-08-27',
                'height' => 174,
                'weight' => 76,
                'mobile_number' => '09170000002',
                'plan_name' => '1st Time Membership',
                'subscription_name' => '1st Time Subscription',
                'profile_status' => 'pending_approval',
                'subscription_status' => 'pending_subscription_approval',
                'approved' => false,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'first_name' => 'Nina',
                'last_name' => 'Cruz',
                'username' => 'local_expired_member',
                'email' => 'local.member.expired@example.test',
                'branch_id' => $branches[2] ?? $branches[0],
                'sex' => 'female',
                'birthday' => '1991-01-18',
                'height' => 158,
                'weight' => 54,
                'mobile_number' => '09170000003',
                'plan_name' => 'Monthly Membership',
                'subscription_name' => 'Monthly Subscription',
                'profile_status' => 'expired',
                'subscription_status' => 'expired',
                'approved' => true,
                'starts_at' => now()->subDays(45),
                'ends_at' => now()->subDays(15),
            ],
        ];

        foreach ($memberRows as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'branch_id' => $row['branch_id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'username' => $row['username'],
                    'password' => Hash::make('password'),
                    'role' => 'member',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $plan = MembershipPlan::query()
                ->where('branch_id', $row['branch_id'])
                ->where('name', $row['plan_name'])
                ->first();

            $subscription = Subscriptions::query()
                ->where('branch_id', $row['branch_id'])
                ->where('name', $row['subscription_name'])
                ->first();

            DB::table('member_profiles')->updateOrInsert(
                ['user_id' => $user->user_id],
                [
                    'branch_id' => $row['branch_id'],
                    'plan_id' => $plan?->plan_id,
                    'subscription_id' => $subscription?->subscription_id,
                    'sex' => $row['sex'],
                    'birthday' => $row['birthday'],
                    'height' => $row['height'],
                    'weight' => $row['weight'],
                    'mobile_number' => $row['mobile_number'],
                    'qr_code' => 'LOCAL-MEMBER-'.$user->user_id,
                    'status' => $row['profile_status'],
                    'subscription_status' => $row['subscription_status'],
                    'renewal_pending' => $row['profile_status'] === 'expired',
                    'isApproved' => $row['approved'],
                    'isApprovedForSubscription' => $row['approved'],
                    'isDisabled' => false,
                    'isDisabledForSubscription' => false,
                    'approved_at' => $row['approved'] ? now() : null,
                    'approved_at_for_subscription' => $row['approved'] ? now() : null,
                    'start_date' => $row['starts_at'],
                    'start_date_for_subscription' => $row['starts_at'],
                    'end_date' => $row['ends_at'],
                    'end_date_for_subscription' => $row['ends_at'],
                    'suspended_at' => null,
                    'days_remaining_before_suspend' => null,
                    'plan_days_remaining_before_suspend' => null,
                ]
            );
        }

        $memberIds = $this->localMemberIds();

        DB::table('attendances')->whereIn('member_id', $memberIds)->delete();

        $activeMember = $this->memberIdForEmail('local.member.active@example.test');
        $expiredMember = $this->memberIdForEmail('local.member.expired@example.test');

        $attendances = [];

        if ($activeMember) {
            foreach ([9, 6, 3] as $daysAgo) {
                $checkIn = now()->subDays($daysAgo)->setTime(17, 30);

                $attendances[] = [
                    'branch_id' => $branches[0],
                    'member_id' => $activeMember,
                    'check_in_time' => $checkIn,
                    'check_out_time' => (clone $checkIn)->addMinutes(95),
                    'duration' => 95,
                    'status' => 'checked_out',
                    'created_at' => $checkIn,
                    'updated_at' => (clone $checkIn)->addMinutes(95),
                ];
            }

            $checkIn = now()->setTime(8, 15);
            $attendances[] = [
                'branch_id' => $branches[0],
                'member_id' => $activeMember,
                'check_in_time' => $checkIn,
                'check_out_time' => null,
                'duration' => null,
                'status' => 'checked_in',
                'created_at' => $checkIn,
                'updated_at' => $checkIn,
            ];
        }

        if ($expiredMember) {
            $checkIn = now()->subDays(35)->setTime(18, 0);

            $attendances[] = [
                'branch_id' => $branches[2] ?? $branches[0],
                'member_id' => $expiredMember,
                'check_in_time' => $checkIn,
                'check_out_time' => (clone $checkIn)->addMinutes(70),
                'duration' => 70,
                'status' => 'checked_out',
                'created_at' => $checkIn,
                'updated_at' => (clone $checkIn)->addMinutes(70),
            ];
        }

        if ($attendances !== []) {
            DB::table('attendances')->insert($attendances);
        }
    }

    private function seedCarts(): void
    {
        $user = User::query()->where('email', 'local.member.active@example.test')->first();
        $products = Product::query()->where('status', 'available')->orderBy('product_id')->limit(2)->get();

        if (! $user || $products->isEmpty()) {
            return;
        }

        $cartIds = DB::table('carts')
            ->where('user_id', $user->user_id)
            ->pluck('cart_id');

        DB::table('cart_items')->whereIn('cart_id', $cartIds)->delete();
        DB::table('carts')->whereIn('cart_id', $cartIds)->delete();

        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $user->user_id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $items = $products->map(function (Product $product, int $index) use ($cartId) {
            $quantity = $index + 1;

            return [
                'cart_id' => $cartId,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $product->price,
                'sub_total' => (int) ($product->price * $quantity),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('cart_items')->insert($items);
    }

    private function seedSalesAndTransactions(): void
    {
        $referenceCodes = ['LOCAL-SALE-1001', 'LOCAL-SALE-1002', 'LOCAL-SALE-1003'];

        DB::table('sales')->whereIn('reference_code', $referenceCodes)->delete();

        $staff = User::query()->where('role', 'staff')->whereNotNull('branch_id')->orderBy('user_id')->first()
            ?? User::query()->whereIn('role', ['admin', 'super_admin'])->orderBy('user_id')->first();

        $activeMember = User::query()->where('email', 'local.member.active@example.test')->first();
        $pendingMember = User::query()->where('email', 'local.member.pending@example.test')->first();
        $products = Product::query()->where('status', 'available')->orderBy('product_id')->limit(3)->get();

        if (! $staff || ! $activeMember || ! $pendingMember || $products->isEmpty()) {
            return;
        }

        $branchId = $staff->branch_id ?? $activeMember->branch_id;
        $plan = MembershipPlan::query()->where('branch_id', $branchId)->where('name', 'Monthly Membership')->first();
        $subscription = Subscriptions::query()->where('branch_id', $branchId)->where('name', 'Monthly Subscription')->first();

        $this->createSale([
            'reference_code' => 'LOCAL-SALE-1001',
            'user_id' => $staff->user_id,
            'branch_id' => $branchId,
            'payment_method' => 'cash',
            'status' => 'paid',
            'type' => 'products',
            'created_at' => now()->subDays(2),
            'items' => $products->take(2)->map(fn (Product $product) => [
                'product_id' => $product->product_id,
                'quantity' => 1,
                'price' => $product->price,
            ])->all(),
        ]);

        if ($plan) {
            $this->createSale([
                'reference_code' => 'LOCAL-SALE-1002',
                'user_id' => $activeMember->user_id,
                'branch_id' => $branchId,
                'payment_method' => 'gcash',
                'status' => 'paid',
                'type' => 'memberships',
                'created_at' => now()->subDays(10),
                'items' => [[
                    'plan_id' => $plan->plan_id,
                    'quantity' => 1,
                    'price' => $plan->price,
                ]],
            ]);
        }

        if ($subscription) {
            $this->createSale([
                'reference_code' => 'LOCAL-SALE-1003',
                'user_id' => $pendingMember->user_id,
                'branch_id' => $branchId,
                'payment_method' => 'gcash',
                'status' => 'pending',
                'type' => 'subscriptions',
                'created_at' => now()->subDay(),
                'items' => [[
                    'subscription_id' => $subscription->subscription_id,
                    'quantity' => 1,
                    'price' => $subscription->price,
                ]],
            ]);
        }
    }

    /**
     * @param  array{
     *     reference_code: string,
     *     user_id: int,
     *     branch_id: int|null,
     *     payment_method: string,
     *     status: string,
     *     type: string,
     *     created_at: Carbon,
     *     items: array<int, array<string, mixed>>
     * }  $sale
     */
    private function createSale(array $sale): void
    {
        $total = collect($sale['items'])->sum(fn (array $item) => $item['price'] * $item['quantity']);

        $saleId = DB::table('sales')->insertGetId([
            'branch_id' => $sale['branch_id'],
            'user_id' => $sale['user_id'],
            'total_amount' => $total,
            'payment_method' => $sale['payment_method'],
            'status' => $sale['status'],
            'type' => $sale['type'],
            'reference_code' => $sale['reference_code'],
            'tax' => 0,
            'discount' => 0,
            'created_at' => $sale['created_at'],
            'updated_at' => now(),
        ]);

        foreach ($sale['items'] as $item) {
            DB::table('sales_items')->insert([
                'sales_id' => $saleId,
                'product_id' => $item['product_id'] ?? null,
                'plan_id' => $item['plan_id'] ?? null,
                'subscription_id' => $item['subscription_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sub_total' => $item['price'] * $item['quantity'],
                'created_at' => $sale['created_at'],
                'updated_at' => now(),
            ]);

            DB::table('transactions')->insert([
                'branch_id' => $sale['branch_id'],
                'sales_id' => $saleId,
                'product_id' => $item['product_id'] ?? null,
                'plan_id' => $item['plan_id'] ?? null,
                'subscription_id' => $item['subscription_id'] ?? null,
                'performed_by' => $sale['user_id'],
                'type' => $sale['type'] === 'products' ? 'sales' : $sale['type'],
                'quantity' => $item['quantity'],
                'created_at' => $sale['created_at'],
                'updated_at' => now(),
            ]);
        }
    }

    private function seedLogs(): void
    {
        $localEmails = [
            'local.member.active@example.test',
            'local.member.pending@example.test',
            'local.member.expired@example.test',
        ];

        $users = User::query()->whereIn('email', $localEmails)->get();

        DB::table('logs')->whereIn('user_id', $users->pluck('user_id'))->delete();

        foreach ($users as $user) {
            DB::table('logs')->insert([
                'user_id' => $user->user_id,
                'branch_id' => $user->branch_id,
                'action' => 'Local testing member seeded',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @return array<int, int>
     */
    private function localMemberIds(): array
    {
        return DB::table('member_profiles')
            ->join('users', 'member_profiles.user_id', '=', 'users.user_id')
            ->whereIn('users.email', [
                'local.member.active@example.test',
                'local.member.pending@example.test',
                'local.member.expired@example.test',
            ])
            ->pluck('member_profiles.member_id')
            ->all();
    }

    private function memberIdForEmail(string $email): ?int
    {
        $memberId = DB::table('member_profiles')
            ->join('users', 'member_profiles.user_id', '=', 'users.user_id')
            ->where('users.email', $email)
            ->value('member_profiles.member_id');

        return $memberId ? (int) $memberId : null;
    }
}
