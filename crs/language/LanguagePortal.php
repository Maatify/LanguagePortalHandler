<?php
/**
 * @PHP       Version >= 8.2
 * @Liberary  LanguagePortalHandler
 * @Project   LanguagePortalHandler
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2025-02-08 11:55 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/LanguagePortalHandler  view project on GitHub
 * @Maatify   LanguagePortalHandler :: LanguagePortal
 */

namespace Maatify\LanguagePortalHandler\Language;

use App\Assist\AppFunctions;
use JetBrains\PhpStorm\NoReturn;
use Maatify\Json\Json;
use Maatify\LanguagePortalHandler\DBHandler\UploaderWebPPortalHandler;

class LanguagePortal extends DbLanguage
{
    private array $languages;

    private static self $instance;

    // Singleton instance getter
    public static function obj(): self
    {
        return self::$instance ??= new self();
    }

    private array $ids = [];
    private array $id_name_code = [];

    public function __construct()
    {
        parent::__construct();
        $this->languages = $this->RowsThisTable();
        foreach ($this->languages as $language) {
            $this->ids[] = $language['language_id'];
            $this->id_name_code[] = [$language['language_id'], $language['name'], $language['code']];
        }
    }

    public function Ids(): array
    {
        return $this->ids;
    }

    public function IdNameCode(): array
    {
        return $this->id_name_code;
    }

    protected function CheckColExist(string $col, string $val): bool
    {
        return $this->RowIsExistThisTable("`$col` = ? ", [$val]);
    }

    public function List(): void
    {
        $result = $this->RowsThisTable();

        $result = array_map(function (array $record) {
            if (! empty($record['image'])) {
                $record['image'] = AppFunctions::SiteImageURL() . $record['image'];
            }

            return $record;
        }, $result);

        $this->JsonData(
            $result,
            line: $this->class_name . __LINE__
        );
    }

    #[NoReturn] public function Update(): void
    {
        $this->PostedLanguageId();
        $this->row_id = $this->language_id;
        $name = $this->postValidator->Optional('name', 'name');
        $short_name = $this->postValidator->Optional('short_name', 'letters');
        $code = $this->postValidator->Optional('code', 'letters');
        $locale = $this->postValidator->Optional('locale', 'string', $this->class_name . __LINE__);
        $directory = $this->postValidator->Optional('directory', 'string', $this->class_name . __LINE__);
        $sort = $this->postValidator->Optional('sort', 'int');
        $current = $this->RowByID();
        $edits = array();
        $changes = array();
        $this->logger_keys[self::IDENTIFY_TABLE_ID_COL_NAME] = $this->language_id;
        $log[self::IDENTIFY_TABLE_ID_COL_NAME] = $this->language_id;
        if (isset($_POST['name']) && $name != $current['name']) {
            if ($this->CheckColExist('name', $name)) {
                Json::Exist('name', 'Name ' . $name . ' Already Exist', $this->class_name . __LINE__);
            } else {
                $edits['name'] = $name;
                $log['name'] = ['from' => $current['name'], 'to' => $name];
                $changes['name'] = ['from' => $current['name'], 'to' => $name];
            }
        }
        if (isset($_POST['short_name']) && $short_name != $current['short_name']) {
            if ($this->CheckColExist('short_name', $short_name)) {
                Json::Exist('short_name', 'Short Name ' . $short_name . ' Already Exist', $this->class_name . __LINE__);
            } else {
                $edits['short_name'] = $short_name;
                $changes['short_name'] = ['from' => $current['short_name'], 'to' => $short_name];
                $log['short_name'] = ['from' => $current['short_name'], 'to' => $short_name];
            }
        }
        if (isset($_POST['code']) && $code != $current['code']) {
            if ($this->CheckColExist('code', $code)) {
                Json::Exist('code', 'Code ' . $code . ' Already Exist', $this->class_name . __LINE__);
            } else {
                $edits['code'] = $code;
                $changes['code'] = ['from' => $current['code'], 'to' => $code];
                $log['code'] = ['from' => $current['code'], 'to' => $code];
            }
        }
        if (isset($_POST['locale']) && $locale != $current['locale']) {
            if ($this->CheckColExist('locale', $locale)) {
                Json::Exist('locale', 'locale ' . $locale . ' Already Exist', $this->class_name . __LINE__);
            } else {
                $edits['locale'] = $locale;
                $changes['locale'] = ['from' => $current['locale'], 'to' => $locale];
                $log['locale'] = ['from' => $current['locale'], 'to' => $locale];
            }
        }
        if (isset($_POST['directory']) && $directory != $current['directory']) {
            $edits['directory'] = $directory;
            $changes['directory'] = ['from' => $current['directory'], 'to' => $directory];
            $log['directory'] = ['from' => $current['directory'], 'to' => $directory];
        }
        if (isset($_POST['sort']) && $sort != $current['sort']) {
            $edits['sort'] = $sort;
            $changes['sort'] = ['from' => $current['sort'], 'to' => $sort];
            $log['sort'] = ['from' => $current['sort'], 'to' => $sort];
        }
        if (empty($edits)) {
            Json::ErrorNoUpdate($this->class_name . __LINE__);
        } else {
            $this->Edit($edits, '`language_id` = ?', [$this->language_id]);
            $this->Logger(logger_description: $log, changes: $changes, action: 'Update');
        }
        Json::Success(line: $this->class_name . __LINE__);
    }

    public function UploadImage(): void
    {
        $this->row_id = $this->ValidatePostedTableId();
        $file = UploaderWebPPortalHandler::obj($this->image_folder)->IconUpload($this->row_id, $this->current_row['short_name'], $this->current_row['image']);
        if (! empty($file['file'])) {
            $log = $this->logger_keys = [self::IDENTIFY_TABLE_ID_COL_NAME => $this->row_id];
            $old_file = ! empty($file['deleted']) ? AppFunctions::SiteImageURL() . $file['deleted'] : '';
            $new_file = AppFunctions::SiteImageURL() . $this->image_folder . '/' . $file['file'];
            $log['image'] = ['from' => $old_file, 'to' => $new_file];
            $changes['image'] = ['from' => $old_file, 'to' => $new_file];
            $this->Edit([
                'image' => /*$this->image_folder . '/' .*/ $file['file'],
            ], '`language_id` = ? ', [$this->row_id]);
            $this->Logger($log, $changes, 'UploadImage');
            Json::Success(line: $this->class_name . __LINE__);
        }
    }

    public function listArray(): array
    {
        return $this->RowsThisTable('`language_id`, `name`');
    }
}