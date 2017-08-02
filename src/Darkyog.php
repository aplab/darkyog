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
     * Sydark constructor.
     */
    public function __construct()
    {
        $this->user = get_current_user();
        $this->path = str_replace(static::USERNAME_PLACEHOLDER, $this->user, static::PATH_TEMPLATE);
        $this->lines = file($this->path);
        $this->sourceData = parse_ini_file($this->path, true, INI_SCANNER_RAW);
    }

    /**
     * invoke
     */
    public function __invoke()
    {
        $this->get('GENERALPREF', 'AutoCompleteTagsDir');
    }

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
                    echo $value;
                }
            }
        }
    }
}