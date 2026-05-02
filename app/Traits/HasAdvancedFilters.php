<?php

namespace App\Traits;

use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

trait HasAdvancedFilters
{
    /**
     * Modelin veritabanı şemasındaki kolonlara bakarak
     * otomatik bir QueryBuilder filtresi oluşturur.
     */
    public static function getAdvancedFilter(string $modelClass): QueryBuilder
    {
        if (!class_exists($modelClass)) {
            return QueryBuilder::make()->constraints([]);
        }

        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();
        
        $constraints = [];
        
        // Veritabanındaki tüm kolon tiplerini çek
        $columns = Schema::getColumns($table);
        
        foreach ($columns as $column) {
            $name = $column['name'];
            $type = strtolower($column['type_name']);
            
            // Kolon tipine göre uygun constraint'i belirle
            if (in_array($type, ['varchar', 'text', 'longtext', 'char', 'string'])) {
                $constraints[] = TextConstraint::make($name)->label(ucfirst(str_replace('_', ' ', $name)));
            } elseif (in_array($type, ['int', 'integer', 'bigint', 'tinyint', 'smallint', 'decimal', 'float', 'double'])) {
                // Eğer boolean olarak kullanılıyorsa (tinyint(1) gibi) is_ önekli kolonları boolean yapabiliriz
                if ($type === 'tinyint' || str_starts_with($name, 'is_') || str_starts_with($name, 'has_')) {
                    $constraints[] = BooleanConstraint::make($name)->label(ucfirst(str_replace('_', ' ', $name)));
                } else {
                    $constraints[] = NumberConstraint::make($name)->label(ucfirst(str_replace('_', ' ', $name)));
                }
            } elseif (in_array($type, ['timestamp', 'datetime', 'date'])) {
                $constraints[] = DateConstraint::make($name)->label(ucfirst(str_replace('_', ' ', $name)));
            } elseif ($type === 'boolean') {
                $constraints[] = BooleanConstraint::make($name)->label(ucfirst(str_replace('_', ' ', $name)));
            }
        }

        return QueryBuilder::make()
            ->label('Gelişmiş Filtre')
            ->constraints($constraints);
    }
}
