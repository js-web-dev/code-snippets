<?php
// to get the file path
// echo plugin_dir_path(__FILE__)
class AssetPacker
{
    const SERVER_ROOT_PATH = '/var/www/vhosts/twire.info/js-web.design/wp-content/plugins/twire-functions/';
    // Stylesheet (CSS) Minifier
    // Credits: https://gist.github.com/Rodrigo54/93169db48194d470188f, https://github.com/mecha-cms/extend.minify
    public static function minifyStyles($input)
    {
        if (trim($input) === "")
            return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                // '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
            $input
        );
    }

    // JavaScript (JS) Minifier
    // Credits: https://gist.github.com/Rodrigo54/93169db48194d470188f, https://github.com/mecha-cms/extend.minify
    public static function minifyScripts($input)
    {
        if (trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
            $input
        );
    }

    public static function bundle($bundleFilePath, array $assetPaths, $minifyFunc = null): string
    {
        // Check  for existing bundle file
        $serverBundleFilePath = self::SERVER_ROOT_PATH . $bundleFilePath;
        if (!is_readable($serverBundleFilePath)) {
            // Open File Stream (Store in memory)
            $stream = fopen('php://memory', 'r+');
            foreach ($assetPaths as $path) {
                // Ensure the asset can be read by PHP
                $serverAssetPath = self::SERVER_ROOT_PATH . $path;
                if (!is_readable($serverAssetPath)) {
                    // todo: throw an exception
                    continue;
                }
                $contents = file_get_contents($serverAssetPath);
                // Minify the content if the file name doesn't include the universal ".min."
                if ($minifyFunc != null && method_exists(self::class, $minifyFunc) && strpos($path, '.min.') === false) {
                    $contents = self::$minifyFunc($contents);
                }
                fwrite($stream, $contents . PHP_EOL);
            }
            rewind($stream);
            file_put_contents($serverBundleFilePath, $stream);
            fclose($stream);
        }
        return $bundleFilePath;
    }

    public static function css($bundleFilePath, array $assetPaths): string
    {
        $bundleFilePath = self::bundle($bundleFilePath, $assetPaths, 'minifyStyles');
        // return '<link rel="stylesheet" href="' .    $bundleFilePath . '">';
        return $bundleFilePath;
    }

    public static function js($bundleFilePath, array $assetPaths): string
    {
        $bundleFilePath = self::bundle($bundleFilePath, $assetPaths, 'minifyScripts');
        // return '<script src="' .    $bundleFilePath . '" defer></script>';
        return $bundleFilePath;
    }
}
