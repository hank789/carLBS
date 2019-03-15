<?php

namespace App\Models\Auth\Traits\Attribute;

use Illuminate\Support\Facades\Hash;

/**
 * Trait UserAttribute.
 */
trait UserCompanyAttribute
{



    /**
     * @return string
     */
    public function getCompanyConfirmedLabelAttribute()
    {
        if ($this->isConfirmed()) {
            if ($this->id != 1 && $this->id != auth()->id()) {
                return '<a href="'.route(
                    'admin.company.user.unconfirm',
                        $this
                ).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.backend.access.users.unconfirm').'" name="confirm_item"><span class="badge badge-success" style="cursor:pointer">'.__('labels.general.yes').'</span></a>';
            } else {
                return '<span class="badge badge-success">'.__('labels.general.yes').'</span>';
            }
        }

        return '<a href="'.route('admin.company.user.confirm', $this).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.backend.access.users.confirm').'" name="confirm_item"><span class="badge badge-danger" style="cursor:pointer">'.__('labels.general.no').'</span></a>';
    }


    /**
     * @return string
     */
    public function getCompanyClearSessionButtonAttribute()
    {
        if ($this->id != auth()->id() && config('session.driver') == 'database') {
            return '<a href="'.route('admin.company.user.clear-session', $this).'"
			 	 data-trans-button-cancel="'.__('buttons.general.cancel').'"
                 data-trans-button-confirm="'.__('buttons.general.continue').'"
                 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
                 class="dropdown-item" name="confirm_item">'.__('buttons.backend.access.users.clear_session').'</a> ';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCompanyShowButtonAttribute()
    {
        return '<a href="'.route('admin.company.user.show', $this).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.view').'" class="btn btn-info"><i class="fas fa-eye"></i></a>';
    }

    /**
     * @return string
     */
    public function getCompanyEditButtonAttribute()
    {
        return '<a href="'.route('admin.company.user.edit', $this).'" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'" class="btn btn-primary"><i class="fas fa-edit"></i></a>';
    }

    /**
     * @return string
     */
    public function getCompanyStatusButtonAttribute()
    {
        if ($this->id != auth()->id()) {
            switch ($this->active) {
                case 0:
                    return '<a href="'.route('admin.company.user.mark', [
                            $this,
                            1,
                        ]).'" class="dropdown-item">'.__('buttons.backend.access.users.activate').'</a> ';

                case 1:
                    return '<a href="'.route('admin.company.user.mark', [
                            $this,
                            0,
                        ]).'" class="dropdown-item">'.__('buttons.backend.access.users.deactivate').'</a> ';

                default:
                    return '';
            }
        }

        return '';
    }


    /**
     * @return string
     */
    public function getCompanyDeleteButtonAttribute()
    {
        if ($this->id != auth()->id() && $this->id != 1) {
            return '<a href="'.route('admin.company.user.destroy', $this).'"
                 data-method="delete"
                 data-trans-button-cancel="'.__('buttons.general.cancel').'"
                 data-trans-button-confirm="'.__('buttons.general.crud.delete').'"
                 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
                 class="dropdown-item">'.__('buttons.general.crud.delete').'</a> ';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCompanyDeletePermanentlyButtonAttribute()
    {
        return '<a href="'.route('admin.company.user.delete-permanently', $this).'" name="confirm_item" class="btn btn-danger" data-trans-title="你确定要永久删除此用户？" data-trans-button-cancel="取消" data-trans-button-confirm="确定"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="'.__('buttons.backend.access.users.delete_permanently').'"></i></a> ';
    }

    /**
     * @return string
     */
    public function getCompanyRestoreButtonAttribute()
    {
        return '<a href="'.route('admin.company.user.restore', $this).'" name="confirm_item" class="btn btn-info" data-trans-title="你确定要恢复此用户？" data-trans-button-cancel="取消" data-trans-button-confirm="确定"><i class="fas fa-sync" data-toggle="tooltip" data-placement="top" title="'.__('buttons.backend.access.users.restore_user').'"></i></a> ';
    }

    /**
     * @return string
     */
    public function getCompanyActionButtonsAttribute()
    {
        if ($this->trashed()) {
            return '
				<div class="btn-group" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
				  '.$this->company_restore_button.'
				  '.$this->company_delete_permanently_button.'
				</div>';
        }

        return '
    	<div class="btn-group" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
		  '.$this->company_show_button.'
		  '.$this->company_edit_button.'

		  <div class="btn-group btn-group-sm" role="group">
			<button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  '.__('labels.general.more').'
			</button>
			<div class="dropdown-menu" aria-labelledby="userActions">
			  '.$this->company_clear_session_button.'
			  '.$this->company_status_button.'
			  '.$this->company_delete_button.'
			</div>
		  </div>
		</div>';
    }
}
