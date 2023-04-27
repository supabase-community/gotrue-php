<?php

class User
{
	public string $id;
	public UserAppMetadata $app_metadata;
	public UserMetadata $user_metadata;
	public string $aud;
	public ?string $confirmation_sent_at;
	public ?string $recovery_sent_at;
	public ?string $email_change_sent_at;
	public ?string $new_email;
	public ?string $invited_at;
	public ?string $action_link;
	public ?string $email;
	public ?string $phone;
	public ?string $created_at;
	public ?string $confirmed_at;
	public ?string $email_confirmed_at;
	public ?string $phone_confirmed_at;
	public ?string $last_sign_in_at;
	public ?string $role;
	public ?string $updated_at;
	public ?UserIdentity $identities;
	public ?string $factors;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->app_metadata = new UserAppMetadata($data->app_metadata);
		$this->user_metadata = new UserMetadata($data->user_metadata);
		$this->aud = $data->aud;
		$this->confirmation_sent_at = $data->confirmation_sent_at;
		$this->recovery_sent_at = $data->recovery_sent_at;
		$this->email_change_sent_at = $data->email_change_sent_at;
		$this->new_email = $data->new_email;
		$this->invited_at = $data->invited_at;
		$this->action_link = $data->action_link;
		$this->email = $data->email;
		$this->phone = $data->phone;
		$this->created_at = $data->created_at;
		$this->confirmed_at = $data->confirmed_at;
		$this->email_confirmed_at = $data->email_confirmed_at;
		$this->phone_confirmed_at = $data->phone_confirmed_at;
		$this->last_sign_in_at = $data->last_sign_in_at;
		$this->role = $data->role;
		$this->updated_at = $data->updated_at;
		$this->identities = new UserIdentity($data->identities);
		$this->factors = $data->factors;
	}
}
