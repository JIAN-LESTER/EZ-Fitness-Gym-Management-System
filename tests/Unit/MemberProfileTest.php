<?php

namespace Tests\Unit;

use App\Models\MemberProfile;
use PHPUnit\Framework\TestCase;

class MemberProfileTest extends TestCase
{
    public function test_a_fully_approved_member_does_not_have_an_incomplete_profile(): void
    {
        $member = new MemberProfile([
            'sex' => 'female',
            'birthday' => '1995-01-01',
            'mobile_number' => '09171234567',
            'status' => 'active',
            'subscription_status' => 'active',
            'isApproved' => true,
            'isApprovedForSubscription' => true,
            'isDisabled' => false,
            'isDisabledForSubscription' => false,
        ]);

        $this->assertTrue($member->isFullyApproved());
        $this->assertFalse($member->hasIncompleteProfile());
    }

    public function test_an_incomplete_member_with_a_plan_and_subscription_needs_approval(): void
    {
        $member = new MemberProfile([
            'plan_id' => 1,
            'subscription_id' => 1,
            'status' => 'inactive',
            'subscription_status' => 'inactive',
            'isApproved' => false,
            'isApprovedForSubscription' => false,
            'isDisabled' => false,
            'isDisabledForSubscription' => false,
        ]);

        $this->assertTrue($member->hasIncompleteProfile());
        $this->assertTrue($member->needsApproval());
    }
}
