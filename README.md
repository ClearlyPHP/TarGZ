# Clearly PHP TarGZ Creation tool

After discovering that PharData has a [known memory leak](https://bugs.php.net/bug.php?id=81017),
and is not recommended to be used _at all_, we needed a standard way of creating TarGz's
that will be simple and portable.

This uses the fantastic code from [splitbrain/php-archive](https://github.com/splitbrain/php-archive) and
is simply a wrapper around it.

## Installation

    composer require clearlyphp/targz

## Usage

The API is extremely simple, and there is 100% test code coverage so you can read them
for detailed examples

    use ClearlyPHP\Tools\TarGz;
    $output = "/tmp/output.tar.gz";
    $t = new TarGz($output);
    $t->addSrcDir("/var/log");
    $t->create();

You can configure if the directory name is included, and if it recurses into subfolders
with the second and third params:

    TarGz::addSrcDir(string $srcdir, bool $includedirname = true, bool $recurse = true)

## Extending

If you want to extend this class, (ie, to add filters) simply modify `TarGz::$files` before
calling create()

# About Clearly PHP

Clearly PHP is a collection of useful tools that [ClearlyIP](https://clearlyip.com) use, and
publish to the world.

ClearlyIP consists of most of the previous FreePBX developers, and we are one of the leaders in
Open Source telephony, along with being active members of the Open Source community! If you're
looking for anything from custom VoIP development, to wholesale high-volume SIP trunking, we can
help you out.

If you're interested in more of our open source code, here's some links to our personal github pages

- [Andrew (tm1000)](https://github.com/tm1000)
- [Francois-Alexandre (Fasa)](https://github.com/f-asa)
- [Julien](https://github.com/julienchabanon)
- [Rob (xrobau)](https://github.com/xrobau)
- [Stas](https://github.com/staskobzar)
