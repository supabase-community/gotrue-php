<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/lib/utils.php';

final class GoTrueApiTest extends TestCase
{
	public function testCreateUser(): void
	{
		$email = 'first.last@iana.org';
		$result = createNewUserWithEmail($email);
	}
}
