<?php

namespace ClearlyPHP\Tools;

use splitbrain\PHPArchive\Archive;
use splitbrain\PHPArchive\FileInfo;
use splitbrain\PHPArchive\Tar;

class TarGz
{
	private string $outfile;
	private array $files = [];

	/**
	 * Create TGZ Helper
	 *
	 * @param string $outfile Filename to write to
	 * @param boolean $clobber Overwrite file if exists
	 * @return void
	 * @throws \Exception If clobbering required
	 */
	public function __construct(string $outfile, bool $clobber = false)
	{
		$this->outfile = $outfile;
		if (file_exists($outfile) && !$clobber) {
			throw new \Exception("$outfile exists and not told to clobber");
		}
	}

	/**
	 * Get files that will/have been compressed, which is
	 * formatted as $name_in_archive => $source_file_on_filesystem
	 *
	 * @return array
	 **/
	public function getFiles(): array
	{
		return $this->files;
	}

	/**
	 * Add a directory to be compressed
	 *
	 * @param string $srcdir Directory on filesystem to add
	 * @param boolean $includedirname Use the dirname prefix
	 * @param boolean $recurse Recurse into this folder
	 * @return self
	 */
	public function addSrcDir(string $srcdir, bool $includedirname = true, bool $recurse = true): self
	{
		if ($includedirname) {
			$destprefix = basename($srcdir);
			$src = dirname($srcdir);
		} else {
			$destprefix = '';
			$src = $srcdir;
		}
		$this->buildFiles($src, $destprefix, $recurse);
		return $this;
	}

	/**
	 * Create the file on disk
	 *
	 * @return string Location of generated file
	 */
	public function create(): string
	{
		if (file_exists($this->outfile)) {
			unlink($this->outfile);
		}
		$tar = new Tar();
		$tar->setCompression(9, Archive::COMPRESS_GZIP);
		$tar->create();
		foreach ($this->files as $name => $srcfile) {
			$fi = FileInfo::fromPath($srcfile, $name);
			$tar->addFile($srcfile, $fi);
		}
		$tar->save($this->outfile);
		return $this->outfile;
	}

	/**
	 * Recursive iterator to generate the filenames required
	 *
	 * @param string $folder Folder to add
	 * @param string $dir Prefix of files
	 * @param boolean $recurse
	 * @return $this
	 */
	public function buildFiles(string $folder, string $dir, bool $recurse = true)
	{
		$i = new \DirectoryIterator("$folder/$dir");
		foreach ($i as $d) {
			if ($d->isDot()) {
				continue;
			}
			if ($d->isDir()) {
				if ($recurse) {
					$newdir = "$dir/" . basename($d->getPathname());
					$this->buildFiles($folder, $newdir, true);
				}
			} else {
				$dest = ltrim("$dir/" . $d->getFilename(), '/');
				$this->files[$dest] = $d->getPathname();
			}
		}
		return $this;
	}
}
