<?php

/**
 *  _                               _ _
 * | |   _   _ _ __   __ _ _ __ ___| | |_   _
 * | |  | | | |  _ \ / _  |  __/ _ \ | | | | |
 * | |__| |_| | | | | (_| | | |  __/ | | |_| |
 * |_____\____|_| |_|\____|_|  \___|_|_|\___ |
 *                                      |___/
 *
 * @author Lunarelly
 * @link https://github.com/Lunarelly
 *
 */

declare(strict_types=1);

namespace lunarelly\personaadapter;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\TypeConverter;

use GdImage;

class SkinLoader extends PluginBase
{
    public static ?SkinLoader $instance = null;
    private Skin $skin;

    public function onEnable(): void
    {
        self::$instance = $this;

        $this->saveResource("default.png", false);
        $skinPaths = glob($this->getDataFolder() . "default.png");

        foreach ($skinPaths as $skinPath) {
            $image = imagecreatefrompng($skinPath);

            if ($image === false) {
                continue;
            }

            $this->skin = new Skin("skin." . basename($skinPath), $this->fromImage($image), "", "geometry.humanoid.custom");
            @imagedestroy($image);
        }

        TypeConverter::getInstance()->setSkinAdapter(new SkinAdapterPersona());
    }

    public static function getInstance(): SkinLoader
    {
        return self::$instance;
    }

    public function fromImage(GdImage $image): string
    {
        $bytes = "";
        for ($y = 0; $y < imagesy($image); $y++) {
            for ($x = 0; $x < imagesx($image); $x++) {
                $rgba = @imagecolorat($image, $x, $y);

                $a = ((~($rgba >> 24)) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;

                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($image);

        return $bytes;
    }

    public function getSkin(): Skin
    {
        return $this->skin;
    }
}
