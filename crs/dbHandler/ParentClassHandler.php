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
 * @Maatify   LanguagePortalHandler :: ParentClassHandler
 */

namespace Maatify\LanguagePortalHandler\DBHandler;

// user for main class like product, info or any other no have language id
use App\DB\DBS\DbPortalHandler;

abstract class ParentClassHandler extends DbPortalHandler
{
    protected string $tableName;
    protected string $identify_table_id_col_name;
    protected string $logger_type;
    protected string $logger_sub_type;
    protected array $logger_keys;
    protected array $logger_changes;

    // to use in list of AllPaginationThisTableFilter()
    protected array $inner_language_tables = [];

    // to use in list of source and destination rows with names
    protected string $inner_language_name_class = '';
    protected array $cols_to_add = [];
    protected array $cols_to_edit = [];
    protected array $cols_to_filter = [];

    // for child classes without language_id
    protected array $child_classes = [];

    // to use in add if child classes have language_id
    protected array $child_classe_languages = [];

    public function MaxIDThisTable(): int
    {
        return parent::MaxIDThisTable();
    }

}