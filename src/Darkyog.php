<?php
/**
 * Created by Alexander Polyanin polyanin@gmail.com.
 * User: apolianin
 * Date: 02.08.2017
 * Time: 15:33
 */

class Darkyog
{
    /**
     * Current user
     *
     * @var string
     */
    protected $user;

    /**
     * Template path to sqlyog.ini
     *
     * @var string
     */
    const PATH_TEMPLATE = 'C:\\Users\\%USERNAME%\\AppData\\Roaming\\SQLyog\\sqlyog.ini';

    /**
     * Username placeholder
     *
     * @var string
     */
    const USERNAME_PLACEHOLDER = '%USERNAME%';

    /**
     * Path to sqlyog.ini
     *
     * @var string
     */
    protected $path;

    /**
     * ini data as array
     *
     * @var array
     */
    protected $sourceData;

    /**
     * lines array
     *
     * @var array
     */
    protected $lines;

    /**
     * Automatically detected line separator
     *
     * @var string
     */
    protected $lineSeparator;

    /**
     * Sydark constructor.
     */
    public function __construct()
    {
        $this->user = get_current_user();
        $this->path = str_replace(static::USERNAME_PLACEHOLDER, $this->user, static::PATH_TEMPLATE);
        $this->lines = file($this->path);
        $this->sourceData = parse_ini_file($this->path, true, INI_SCANNER_RAW);
        $this->detectLineSeparator();
    }

    private function detectLineSeparator()
    {
        $this->lineSeparator = hex2bin('0d0a');
        $line = bin2hex(reset($this->lines));
        $test = substr($line, -4, 4);
        if ($test !== $this->lineSeparator) {
            $this->lineSeparator = PHP_EOL;
        }
    }

    /**
     * invoke
     */
    public function __invoke()
    {
        $this->set('GENERALPREF', 'EditorBgColor', static::rgb('232525'));
        $this->set('GENERALPREF', 'NormalColor', static::rgb('cc7832'));
        $this->set('GENERALPREF', 'NumberColor', static::rgb('6897bb'));

        $this->set('GENERALPREF', 'NumberMarginbackgroundColor', static::rgb('313335'));
        $this->set('GENERALPREF', 'NumberMarginforegroundColor', static::rgb('5C6259'));

        $this->set('GENERALPREF', 'FoldingMarginFgColor', static::rgb('555555'));
        $this->set('GENERALPREF', 'FoldingMarginbackgroundColor', static::rgb('313335'));
        $this->set('GENERALPREF', 'FoldingMarginTextureColor', static::rgb('313335'));

        $this->set('GENERALPREF', 'CanvasBgColor', static::rgb('313335'));
        $this->set('GENERALPREF', 'CanvasLineColor', static::rgb('313335'));
        $this->set('GENERALPREF', 'CanvasTextColor', static::rgb('313335'));

        $this->set('GENERALPREF', 'MTIBgColor', static::rgb('232525'));
        $this->set('GENERALPREF', 'MTIFgColor', static::rgb('ffffff'));
        $this->set('GENERALPREF', 'MTISelectionColor', static::rgb('232525'));

        $this->set('Themedetails', 'ThemeFile', 'Dark.xml');
        $this->set('Themedetails', 'ThemeType', '2');

        for ($i = 1; $i < 100; $i++) {
            $this->set('Connection ' . $i, 'ObjectbrowserBkcolor', static::rgb('3C3F41'));
            $this->set('Connection ' . $i, 'ObjectbrowserFgcolor', static::rgb('ffffff'));
        }

        mkdir(dirname($this->path) . '/themes');
        copy(__DIR__ . '/Dark.xml', dirname($this->path) . '/themes/Dark.xml');

        echo $this->store() . ' bytes written' . PHP_EOL;
    }

    protected static function rgb($rrggbb)
    {
        return hexdec(join(array_reverse(str_split($rrggbb, 2))));
    }

    /**
     * Getter
     *
     * @param $section
     * @param $name
     * @return bool|string
     */
    public function get($section, $name)
    {
        $section_found = false;
        $section_ready = '[' . $section . ']';
        $name_ready = $name . '=';
        foreach ($this->lines as $line) {
            $line_ready = trim($line);
            if ($line_ready === $section_ready) {
                $section_found = true;
                continue;
            }
            if ($section_found) {
                if (0 === strpos($line_ready, $name_ready)) {
                    $value = substr($line_ready, strlen($name_ready));
                    return $value;
                }
            }
        }
    }

    /**
     * Setter
     *
     * @param $section
     * @param $name
     * @param $value
     */
    public function set($section, $name, $value)
    {
        $section_found = false;
        $section_ready = '[' . $section . ']';
        $name_ready = $name . '=';
        foreach ($this->lines as $line_number => $line) {
            $line_ready = trim($line);
            if ($line_ready === $section_ready) {
                $section_found = true;
                continue;
            }
            if ($section_found) {
                if (0 === strpos($line_ready, $name_ready)) {
                    $this->lines[$line_number] = $name_ready . $value . $this->lineSeparator;
                }
            }
        }
    }

    public function store()
    {
        return file_put_contents($this->path, join('', $this->lines));
    }
}