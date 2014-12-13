<?php

class User {
	public $username;

	public function __construct($username) {
		$this->username = $username;
	}

	public function isGuest() {
		return $this->username == '';
  }

	public function isUser() {
		return $this->isAdmin() || $this->username == 'user';
  }

	public function isAdmin() {
		return $this->username == 'admin';
  }
}
