<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Model implements Authenticatable, CanResetPassword {

  use \Illuminate\Notifications\Notifiable;

  protected $fillable = ["email", "password"];

  private $rememberToken;
  private $rememberTokenName;

  /**
   * Get the name of the unique identifier for the user.
   *
   * @return string
   */
  public function getAuthIdentifierName() {
    return "id";
  }

  /**
   * Get the unique identifier for the user.
   *
   * @return mixed
   */
  public function getAuthIdentifier() {
    return $this->id;
  }

  /**
   * Get the password for the user.
   *
   * @return string
   */
  public function getAuthPassword() {
    return $this->password;
  }

  /**
   * Get the token value for the "remember me" session.
   *
   * @return string
   */
  public function getRememberToken() {
    return $this->rememberToken;
  }

  /**
   * Set the token value for the "remember me" session.
   *
   * @param  string  $value
   * @return void
   */
  public function setRememberToken($value) {
    $this->rememberToken = $value;
  }

  /**
   * Get the column name for the "remember me" token.
   *
   * @return string
   */
  public function getRememberTokenName() {
    return "rememberToken";
  }

  public function getEmailForPasswordReset() {
    return $this->email;
  }

  public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
  }

}
