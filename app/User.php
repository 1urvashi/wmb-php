<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $adminRole = 0;
    protected $inspectorRole = 1;
    protected $supervisorRole = 2;
    protected $auctionManagerRole = 3;
    protected $branchManagerRole = 4;
    protected $qualityControlRole = 5;
    protected $seniorManagerRole = 6;
    protected $dealerHeadRole = 7;
    protected $documentControllerRole = 8;
    protected $drmUser = 9;
    protected $headDrmUser = 10;
    //exportBranchManeges
    //exportDrm
    //exportHistory
    //exportAdminUsers

    protected $drmUserAllowedPermissions = [
        'changePasswordMenu',
        'tradersMenu',
        // 'hideCreateTraderButton',
        'vehiclesMenu',
        'newVehiclesMenu',
        'vehiclesUnderAuctionMenu',
        'noAuctionViewDetails',
        // 'noAuctionDelete',
        'objectEdit',
        'ongoingAuction',
        'OngoingAuctionAction',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'closeAuctionReopenAction',
        'cancelAuctionButton',
        'scheduledAuctionMenu',
        'cancelledAuctionMenu',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'soldAuctionMenu',
        'closeAuctionNegotiateAction',
        'viewAuction',
        'OngoingAuctionView',
        'cacncelClosedAmount',
        // 'closeAuctionOverrideBidAmount',
        'closeAuctionOwnerOverrideBidAmount',
        // 'closeAuctionQAAction',
        // 'passbuttonView',
        // 'failbuttonView',
        'bidOwnerView',
        'bidHistory',
        'customerAmount',
        'basePriceView',
        'minPriceView',
        'buyPriceView',
        'cashAuctionMenu',
        'hideReopenBtn',
        'hideCancelBtn',
        'disableVinForVehicle',
        'historyMenu',
        'noAuctionSubmitAuction',
        'currentPriceView',
    ];
    protected $inspectorAllowedPermissions = [
        'vehiclesMenu',
        'newVehiclesMenu',
        'noAuctionSubmitAuction',
        'viewAuction',
    ];
    protected $supervisorAllowedPermissions = [
        'vehiclesMenu',
        'newVehiclesMenu',
        'noAuctionSubmitAuction',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'closeAuctionNegotiateAction',
        'closeAuctionInspectorNegotiateAction',
        'viewAuction',
    ];
    //buyPriceView
    //basePriceView
    //currentPriceView
    //customerAmount
    //OngoingAuctionView
    //viewAuction
    //cancelAuctionButton
    ///readySaleButton
    //bidHistory
    //customersDetail
    //bidOwnerView

    /*
      DRM Prmissions
     */
    //drmView
    //drmCreate
    //drmUpdate
    //drmDelete

    protected $auctioManagerAllowedPermissions = [
        'vehiclesMenu',
        'newVehiclesMenu',
        'noAuctionSubmitAuction',
        'vehiclesUnderAuctionMenu',
        'OngoingAuctionAction',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'closeAuctionReopenAction',
        'changePasswordMenu',
        'cancelAuctionButton',
        'noAuctionViewDetails',
        'objectEdit',
        'scheduledAuctionMenu',
        'cancelledAuctionMenu',
        'viewAuction',
        'cacncelClosedAmount',
        'closeAuctionNegotiateAction',
        'closeAuctionInspectorNegotiateAction',
        'bidOwnerView',
        'bidHistory',
        'notificationViewAction',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'soldAuctionMenu',
        'bidOwnerView',
        'bidHistory',
        'customerAmount',
        'basePriceView',
        'minPriceView',
        'buyPriceView',
        'dashviewAction',
    ];
    protected $headDrmUserAllowedPermissions = [
        'changePasswordMenu',
        'drmView',
        'drmUpdate',
        'drmDelete',
        'vehiclesMenu',
        'newVehiclesMenu',
        'vehiclesUnderAuctionMenu',
        'noAuctionViewDetails',
        'noAuctionDelete',
        'objectEdit',
        'ongoingAuction',
        'OngoingAuctionAction',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'closeAuctionReopenAction',
        'cancelAuctionButton',
        'scheduledAuctionMenu',
        'cancelledAuctionMenu',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'soldAuctionMenu',
        'closeAuctionNegotiateAction',
        'viewAuction',
        'OngoingAuctionView',
        'cacncelClosedAmount',
        'closeAuctionOverrideBidAmount',
        'closeAuctionOwnerOverrideBidAmount',
        'closeAuctionQAAction',
        'passbuttonView',
        'failbuttonView',
        'bidOwnerView',
        'bidHistory',
        'tradersMenu',
        'TraderStatus',
        'mergeTrader',
        'historyMenu',
        'basePriceView',
        'buyPriceView',
        'noAuctionSubmitAuction',
        'currentPriceView',
        'bidHistory',
        'customerAmount',
    ];
    protected $branchManagerRoleAllowedPermissions = [
        'vehiclesMenu',
        'newVehiclesMenu',
        'noAuctionSubmitAuction',
        'vehiclesUnderAuctionMenu',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'closeAuctionReopenAction',
        'OngoingAuctionAction',
        //'customersMenu',
        'cancelAuctionButton',
        'viewAuction',
    ];
    protected $qualityControlRoleAllowedPermissions = [
        'auctionsMenu',
        'noAuctionSubmitAuction',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'changePasswordMenu',
        'passbuttonView',
        'failbuttonView',
        'vehiclesMenu',
        'inspectorsMenu',
        'viewAuction',
        'downloadObjectAction',
    ];
    protected $documentControllerRoleAllowedPermissions = [
        'auctionsMenu',
        'soldAuctionMenu',
        'cashAuctionMenu',
        'cashedAction',
        'changePasswordMenu',
        'printSoldAuction',
        'viewAuction',
        'passedQCAuctionMenu',
            // 'dealersMenu',
    ];
    protected $dealerHeadRoleAllowedPermissions = [
        'auctionsMenu',
        'noAuctionSubmitAuction',
        'passedQCAuctionMenu',
        'passbuttonView',
        'failbuttonView',
        'dealersMenu',
        'tradersMenu',
        'inspectorsMenu',
        'soldAuctionMenu',
        'cashAuctionMenu',
        'TraderStatus',
        'viewAuction',
    ];
    protected $seniorManagerRoleAllowedPermissions = [
        'auctionsMenu',
        'minPriceView',
        'noAuctionSubmitAuction',
        'OngoingAuctionAction',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'passbuttonView',
        'failbuttonView',
        'dealersMenu',
        'tradersMenu',
        'inspectorsMenu',
        'vehiclesMenu',
        'customersMenu',
        'newVehiclesMenu',
        'vehiclesUnderAuctionMenu',
        'auctionsMenu',
        'ongoingAuctionMenu',
        'closedAuctionMenu',
        'underAuctionMenu',
        'passedQCAuctionMenu',
        'failedQCAuctionMenu',
        'soldAuctionMenu',
        'cashAuctionMenu',
        'scheduledAuctionMenu',
        'cancelledAuctionMenu',
        'historyMenu',
        'AttributeSetMenu',
        'AttributeMenu',
        'MakeMenu',
        'ModelMenu',
        'bankMenu',
        'usersMenu',
        'changePasswordMenu',
        'versionMenu',
        'buyPriceView',
        'basePriceView',
        'currentPriceView',
        'customerAmount',
        'OngoingAuctionView',
        'viewAuction',
        'cancelAuctionButton',
        'readySaleButton',
        'closeAuctionReopenAction',
        'closeAuctionNegotiateAction',
        'closeAuctionViewAction',
        'closeAuctionQAAction',
        'closeAuctionNegotiateAction',
        'closeAuctionOverrideBidAmount',
        'closeAuctionOwnerOverrideBidAmount',
        'cacncelClosedAmount',
        'dashviewAction',
        'notificationViewAction',
        'sendReminderAction',
        'cashedAction',
        'downloadObjectAction',
        'objectEdit',
        'exportVehicle',
        'updateInspector',
        'vehicleDelete',
        'inspectorCreate',
        'closeAuctionInspectorNegotiateAction',
        'closeAuctionOwnerOverrideBidAmount',
        'TraderStatus',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'image', 'mobile', 'status', 'session_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getDRM() {
        return $this->drmUser;
    }

    public function isAllowed($action) {
        if (!$action || is_null($action)) {
            return true;
        }
        if ($this->role == $this->inspectorRole) {
            $permission = array_search($action, $this->inspectorAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->supervisorRole) {
            $permission = array_search($action, $this->supervisorAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->auctionManagerRole) {
            $permission = array_search($action, $this->auctioManagerAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->drmUser) {
            $permission = array_search($action, $this->drmUserAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->headDrmUser) {
            $permission = array_search($action, $this->headDrmUserAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->branchManagerRole) {
            $permission = array_search($action, $this->branchManagerRoleAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->qualityControlRole) {
            $permission = array_search($action, $this->qualityControlRoleAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->documentControllerRole) {
            $permission = array_search($action, $this->documentControllerRoleAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->seniorManagerRole) {
            $permission = array_search($action, $this->seniorManagerRoleAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } elseif ($this->role == $this->dealerHeadRole) {
            $permission = array_search($action, $this->dealerHeadRoleAllowedPermissions);
            if ($permission > -1) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    //

    public function getRole($roleId) {
        switch ($roleId) {
            case $this->inspectorRole:
                return 'Inspector';
                break;
            case $this->supervisorRole:
                return 'Supervisor';
                break;
            case $this->auctionManagerRole:
                return 'Auction Controller';
                break;
            case $this->branchManagerRole:
                return 'Branch Manager';
                break;
            case $this->qualityControlRole:
                return 'Quality Control';
                break;
            case $this->documentControllerRole:
                return 'Document Controller';
                break;
            case $this->seniorManagerRole:
                return 'Senior Manager';
                break;
            case $this->dealerHeadRole:
                return 'Trader relationship manager';
                break;
            case $this->drmUser:
                return 'DRM';
                break;
            case $this->headDrmUser:
                return 'Head DRM';
            default:
                return 'Admin';
                break;
        }
    }

    public function getImageAttribute($value) {
        return $value ? cdn(config('app.fileDirectory') . 'users/') . $value : null;
        //return $value ? url('uploads/traders/images/'.$value) : null;
    }

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role) {
        if (is_string($role)) {
            return $this->$roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    /* public function timeHistory(){
      return $this->hasOne('App\AdminLogHistory','user_id','id')->latest();
      ->where('type', 'login')
      ->orderBy('time', 'DESC')->first();
      } */
}
