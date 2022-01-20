<?php

use ClearlyPHP\Tools\TarGz;
use PHPUnit\Framework\TestCase;

class TarGzTest extends TestCase
{
	public static function getParentDirName(): string
	{
		$cwd = getcwd();
		chdir(__DIR__ . "/../");
		$parent = getcwd();
		chdir($cwd);
		return $parent;
	}

	public function tearDown(): void
	{
		if (file_exists('/tmp/foo.tar.gz')) {
			unlink('/tmp/foo.tar.gz');
		}
	}

	/** @return void */
	public function testDoNotClobber()
	{
		$this->expectException(\Exception::class);
		file_put_contents("/tmp/foo.tar.gz", "Test contents");
		$z = new TarGz('/tmp/foo.tar.gz');
	}

	/** @return void */
	public function testClobber()
	{
		file_put_contents("/tmp/foo.tar.gz", "Test contents");
		$z = new TarGz('/tmp/foo.tar.gz', true);
		$parent = self::getParentDirName();
		$z->addSrcDir($parent, true, false);
		$z->create();
		$this->assertFileExists('/tmp/foo.tar.gz');
	}

	/** @return void */
	public function testCreateTgzWithPrefix()
	{
		$z = new TarGz('/tmp/foo.tar.gz', true);
		$this->assertEmpty($z->getFiles());
		$parent = self::getParentDirName();
		$pkgname = basename($parent);
		// Do NOT recurse
		$z->addSrcDir($parent, true, false);
		$this->assertArrayHasKey("$pkgname/LICENSE", $z->getFiles());
		$this->assertArrayHasKey("$pkgname/composer.json", $z->getFiles());
		$iam = "$pkgname/tests/" . basename(__FILE__);
		$this->assertArrayNotHasKey($iam, $z->getFiles());
		$z->create();
		$this->assertFileExists('/tmp/foo.tar.gz');
		// Use the system tar to extract it to make sure it's sane
		exec('tar ztvf /tmp/foo.tar.gz', $out, $ret);
		$this->assertEquals(0, $ret, 'tar ztvf /tmp/foo.tar.gz failed');
		$joined = join("\n", $out);
		$this->assertStringContainsString("$pkgname/LICENSE", $joined);
	}

	/** @return void */
	public function testCreateTgzRecursiveWithPrefix()
	{
		$z = new TarGz('/tmp/foo.tar.gz', true);
		$this->assertEmpty($z->getFiles());
		$parent = self::getParentDirName();
		$pkgname = basename($parent);
		// Recurse into subfolders, add dirname are the defaults
		$z->addSrcDir($parent);
		$iam = "$pkgname/tests/" . basename(__FILE__);
		$this->assertArrayHasKey("$pkgname/LICENSE", $z->getFiles());
		$this->assertArrayHasKey("$pkgname/composer.json", $z->getFiles());
		$this->assertArrayHasKey($iam, $z->getFiles());
		$outfile = $z->create();
		$this->assertEquals("/tmp/foo.tar.gz", $outfile);
		$this->assertFileExists('/tmp/foo.tar.gz');
		// Use the system tar to extract it to make sure it's sane
		exec('tar ztvf /tmp/foo.tar.gz', $out, $ret);
		$this->assertEquals(0, $ret, 'tar ztvf /tmp/foo.tar.gz failed');
		$joined = join("\n", $out);
		$this->assertStringContainsString("$pkgname/LICENSE", $joined);
		$this->assertStringContainsString($iam, $joined);
	}


	/** @return void */
	public function testCreateTgzWithoutPrefix()
	{
		$z = new TarGz('/tmp/foo.tar.gz', true);
		$this->assertEmpty($z->getFiles());
		$parent = self::getParentDirName();
		$z->addSrcDir($parent, false, false);
		$this->assertArrayHasKey("LICENSE", $z->getFiles());
		$this->assertArrayHasKey("composer.json", $z->getFiles());
		$iam = "tests/" . basename(__FILE__);
		$this->assertArrayNotHasKey($iam, $z->getFiles());
		$z->create();
		$this->assertFileExists('/tmp/foo.tar.gz');
		// Use the system tar to extract it to make sure it's sane
		exec('tar ztvf /tmp/foo.tar.gz', $out, $ret);
		$this->assertEquals(0, $ret, 'tar ztvf /tmp/foo.tar.gz failed');
		$joined = join("\n", $out);
		$this->assertStringContainsString("LICENSE", $joined);
	}

	/** @return void */
	public function testCreateTgzRecursiveWithoutPrefix()
	{
		$z = new TarGz('/tmp/foo.tar.gz', true);
		$this->assertEmpty($z->getFiles());
		$parent = self::getParentDirName();
		$z->addSrcDir($parent, false);
		$this->assertArrayHasKey("LICENSE", $z->getFiles());
		$this->assertArrayHasKey("composer.json", $z->getFiles());
		$iam = "tests/" . basename(__FILE__);
		$this->assertArrayHasKey($iam, $z->getFiles());
		$z->create();
		$this->assertFileExists('/tmp/foo.tar.gz');
		// Use the system tar to extract it to make sure it's sane
		exec('tar ztvf /tmp/foo.tar.gz', $out, $ret);
		$this->assertEquals(0, $ret, 'tar ztvf /tmp/foo.tar.gz failed');
		$joined = join("\n", $out);
		$this->assertStringContainsString("LICENSE", $joined);
		$this->assertStringContainsString($iam, $joined);
	}
}
