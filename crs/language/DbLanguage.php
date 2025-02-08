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
 * @Maatify   LanguagePortalHandler :: DbLanguage
 */

namespace Maatify\LanguagePortalHandler\Language;

use Maatify\LanguagePortalHandler\Tables\LanguageTable;

class DbLanguage extends LanguageTable
{

    private static self $instance;

    // Singleton instance getter
    public static function obj(): self
    {
        return self::$instance ??= new self();
    }

    public function JOINTableAdd($table_name): string
    {
        return " INNER JOIN `$this->tableName` ON `$this->tableName`.`$this->identify_table_id_col_name` = `$table_name`.`$this->identify_table_id_col_name` ";
    }

    public function JoinShortName(): string
    {
        return "`$this->tableName`.`short_name` as language";
    }

    public function JoinShortNameWithoutAs(): string
    {
        return "`$this->tableName`.`short_name`";
    }

    public function GetCurrentLanguageId(string $short_code): int
    {
        if (! $id = (int)$this->ColThisTable('language_id', '`short_name` = ? ', [strtolower($short_code)])) {
            $id = 1;
        }

        return $id;
    }

    public function RowByID(): array
    {
        return $this->RowThisTable('*', "`$this->identify_table_id_col_name` = ? ", [$this->language_id]);
    }

    public function ShortNameByID(int $language_id): string
    {
        if ($short_name = $this->ColThisTable('short_name', "`$this->identify_table_id_col_name` = ? ", [strtolower($language_id)])) {
            return $short_name;
        }

        return '';
    }

    public function activeListToSelect(): array
    {
        return $this->RowsThisTable(
            "`$this->identify_table_id_col_name`, `name`, `short_name`, `image`", "`status` = ? ORDER BY `sort` DESC", [1]
        );
    }
}