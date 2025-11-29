<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $attendance_id
 * @property int $member_id
 * @property \Illuminate\Support\Carbon|null $check_in_time
 * @property string|null $check_out_time
 * @property string $status
 * @property int|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MemberProfile $member
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAttendanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckInTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckOutTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAttendance {}
}

namespace App\Models{
/**
 * @property int $cart_id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCart {}
}

namespace App\Models{
/**
 * @property int $cart_item_id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property string $price
 * @property int $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCartItem {}
}

namespace App\Models{
/**
 * @property int $category_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categories whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCategories {}
}

namespace App\Models{
/**
 * @property int $inventory_id
 * @property int $product_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInventory {}
}

namespace App\Models{
/**
 * @property int $log_id
 * @property int $user_id
 * @property string $action
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLogs {}
}

namespace App\Models{
/**
 * @property int $member_id
 * @property int $user_id
 * @property int|null $plan_id
 * @property string|null $sex
 * @property string|null $birthday
 * @property float|null $height
 * @property float|null $weight
 * @property string|null $mobile_number
 * @property string|null $qr_code
 * @property string $status
 * @property int $isApproved
 * @property int $isDisabled
 * @property string|null $approved_at
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $renewal_pending
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\MembershipPlan|null $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile expiringSoon(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereIsDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereRenewalPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberProfile whereWeight($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMemberProfile {}
}

namespace App\Models{
/**
 * @property int $plan_id
 * @property string $name
 * @property string|null $details
 * @property string $price
 * @property int $duration_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemberProfile> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan whereDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MembershipPlan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMembershipPlan {}
}

namespace App\Models{
/**
 * @property int $product_id
 * @property int $category_id
 * @property string $name
 * @property string|null $image
 * @property string|null $description
 * @property string $price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Categories $category
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockIn> $stockIns
 * @property-read int|null $stock_ins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOut> $stockOuts
 * @property-read int|null $stock_outs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProduct {}
}

namespace App\Models{
/**
 * @property int $sales_id
 * @property int $user_id
 * @property string $total_amount
 * @property string $tax
 * @property string $discount
 * @property string $payment_method
 * @property string|null $reference_code
 * @property string $status
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Transactions|null $transaction
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereReferenceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sales whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSales {}
}

namespace App\Models{
/**
 * @property int $sales_item_id
 * @property int $sales_id
 * @property int $product_id
 * @property int $quantity
 * @property string $price
 * @property string $sub_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Sales $sale
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereSalesItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSalesItem {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockIn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockIn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockIn query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStockIn {}
}

namespace App\Models{
/**
 * @property int $stock_out_id
 * @property int $product_id
 * @property int $quantity
 * @property string $date
 * @property string $reason
 * @property int|null $related_sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereRelatedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereStockOutId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockOut whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStockOut {}
}

namespace App\Models{
/**
 * @property int $transaction_id
 * @property string $type
 * @property int|null $sales_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Sales|null $sale
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTransactions {}
}

namespace App\Models{
/**
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $status
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cart> $carts
 * @property-read int|null $carts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Logs> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\MemberProfile|null $member
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sales> $sales
 * @property-read int|null $sales_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

