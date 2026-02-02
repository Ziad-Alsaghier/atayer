<?php
/**
 * Laravel 8 - Final Smart Migration & Seeder Generator (Stable Version)
 * --------------------------------------------------------------------
 * ✅ يولد ميجريشن شامل بجميع الجداول والعلاقات
 * ✅ يمنع تكرار المفاتيح أو الأعمدة
 * ✅ يولد Seeder ذكي يحترم ترتيب العلاقات
 * ✅ متوافق مع PHP 8.2 و Laravel 8.x
 */

$modelsPath = __DIR__ . '/app/Models';
$migrationPath = __DIR__ . '/database/migrations';
$seederPath = __DIR__ . '/database/seeders';

if (!is_dir($modelsPath)) {
    exit("❌ لم يتم العثور على مجلد Models في: $modelsPath\n");
}

$migrationFile = $migrationPath . '/2025_10_14_000000_full_schema.php';
$seederFile = $seederPath . '/DatabaseSeeder.php';

function pluralize($word) {
    $end = substr($word, -1);
    if ($end === 'y') return substr($word, 0, -1) . 'ies';
    elseif (in_array($end, ['s', 'x', 'z']) || in_array(substr($word, -2), ['ch', 'sh']))
        return $word . 'es';
    else
        return $word . 's';
}

$tables = [];
$relations = [];

/**
 * قراءة وتحليل المودلز من app/Models
 */
foreach (glob($modelsPath . '/*.php') as $file) {
    $content = file_get_contents($file);
    $modelName = basename($file, '.php');
    $tableName = pluralize(strtolower($modelName));

    // استخراج الأعمدة من fillable
    preg_match('/protected\s+\$fillable\s*=\s*\[([^\]]*)\]/', $content, $match);
    $fillable = [];
    if (!empty($match[1])) {
        preg_match_all("/'([^']+)'/", $match[1], $cols);
        $fillable = $cols[1];
    }

    // استخراج العلاقات belongsTo
    preg_match_all('/belongsTo\(\s*([A-Za-z0-9_\\\\]+)::class/', $content, $belongsMatches);
    foreach ($belongsMatches[1] as $related) {
        $related = trim(basename(str_replace('\\', '/', $related)));
        $relatedTable = pluralize(strtolower($related));
        $foreignKey = strtolower($related) . '_id';
        $fillable[] = $foreignKey;
        $relations[$tableName][] = [
            'column' => $foreignKey,
            'references' => 'id',
            'on' => $relatedTable
        ];
    }

    $tables[$tableName] = array_unique($fillable);
}

// ترتيب الجداول بحيث الجداول الأب تُنشأ أولاً
uksort($tables, function ($a, $b) use ($relations) {
    $aHasFk = isset($relations[$a]);
    $bHasFk = isset($relations[$b]);
    return $aHasFk <=> $bHasFk;
});

/**
 * إنشاء كود الميجريشن
 */
$migrationCode = "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration {\n    public function up(): void\n    {\n";

foreach ($tables as $table => $columns) {
    $migrationCode .= "        Schema::create('$table', function (Blueprint \$table) {\n";
    $migrationCode .= "            \$table->engine = 'InnoDB';\n";
    $migrationCode .= "            \$table->id();\n";

    foreach ($columns as $col) {
        if (in_array($col, ['created_at', 'updated_at'])) continue;

        if (str_ends_with($col, '_id')) {
            $migrationCode .= "            \$table->unsignedBigInteger('$col')->nullable();\n";
        } elseif (preg_match('/(price|amount|total)/i', $col)) {
            $migrationCode .= "            \$table->decimal('$col', 10, 2)->nullable();\n";
        } elseif (preg_match('/(status|type)/i', $col)) {
            $migrationCode .= "            \$table->tinyInteger('$col')->default(0);\n";
        } elseif (preg_match('/(date|time)/i', $col)) {
            $migrationCode .= "            \$table->timestamp('$col')->nullable();\n";
        } else {
            $migrationCode .= "            \$table->string('$col')->nullable();\n";
        }
    }

    $migrationCode .= "            \$table->timestamps();\n";
    $migrationCode .= "        });\n\n";
}

// إضافة العلاقات بعد إنشاء الجداول
$migrationCode .= "        // ====== إضافة المفاتيح الأجنبية (Foreign Keys) ======\n";
foreach ($relations as $table => $rels) {
    foreach ($rels as $rel) {
        if (array_key_exists($rel['on'], $tables)) {
            $fkName = 'fk_' . $table . '_' . $rel['column'] . '_' . uniqid();
            $migrationCode .= "        Schema::table('$table', function (Blueprint \$table) {\n";
            $migrationCode .= "            \$table->foreign('{$rel['column']}', '$fkName')\n";
            $migrationCode .= "                  ->references('{$rel['references']}')\n";
            $migrationCode .= "                  ->on('{$rel['on']}')\n";
            $migrationCode .= "                  ->onDelete('set null');\n";
            $migrationCode .= "        });\n";
        }
    }
}

$migrationCode .= "    }\n\n    public function down(): void\n    {\n";
foreach ($tables as $table => $columns) {
    $migrationCode .= "        Schema::dropIfExists('$table');\n";
}
$migrationCode .= "    }\n};\n";

file_put_contents($migrationFile, $migrationCode);

/**
 * إنشاء Seeder ذكي يحترم العلاقات
 */
$seederCode = "<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse Illuminate\\Support\\Facades\\DB;\n\nclass DatabaseSeeder extends Seeder\n{\n    public function run(): void\n    {\n        // ====== الجداول الأب أولاً ======\n";
$insertedTables = [];

// جداول بدون علاقات
foreach ($tables as $table => $columns) {
    if (!isset($relations[$table])) {
        $data = [];
        foreach ($columns as $col) {
            if (in_array($col, ['created_at', 'updated_at'])) continue;

            if (str_ends_with($col, '_id')) {
                $data[] = "'$col' => null";
            } elseif (preg_match('/(price|amount|total)/i', $col)) {
                $data[] = "'$col' => 100.00";
            } elseif (preg_match('/(status|type)/i', $col)) {
                $data[] = "'$col' => 1";
            } elseif (preg_match('/(date|time)/i', $col)) {
                $data[] = "'$col' => now()";
            } else {
                $data[] = "'$col' => 'example_$col'";
            }
        }
        $cols = implode(', ', $data);
        $seederCode .= "        DB::table('$table')->insert([" . ($cols ? "[$cols]" : "") . "]);\n";
        $insertedTables[] = $table;
    }
}

// الجداول التابعة بعد الآباء
$seederCode .= "\n        // ====== الجداول التي تحتوي على علاقات ======\n";
foreach ($relations as $table => $rels) {
    $data = [];
    foreach ($tables[$table] as $col) {
        if (in_array($col, ['created_at', 'updated_at'])) continue;

        if (str_ends_with($col, '_id')) {
            $parent = pluralize(str_replace('_id', '', $col));
            $data[] = "'$col' => DB::table('$parent')->value('id')";
        } elseif (preg_match('/(price|amount|total)/i', $col)) {
            $data[] = "'$col' => 100.00";
        } elseif (preg_match('/(status|type)/i', $col)) {
            $data[] = "'$col' => 1";
        } elseif (preg_match('/(date|time)/i', $col)) {
            $data[] = "'$col' => now()";
        } else {
            $data[] = "'$col' => 'example_$col'";
        }
    }
    $cols = implode(', ', $data);
    $seederCode .= "        DB::table('$table')->insert([" . ($cols ? "[$cols]" : "") . "]);\n";
}

$seederCode .= "    }\n}\n";

file_put_contents($seederFile, $seederCode);

echo "✅ تم إنشاء الميجريشن في: $migrationFile\n";
echo "✅ وتم إنشاء Seeder في: $seederFile\n";
echo "✨ الآن يمكنك تشغيل:\n";
echo "   php artisan migrate:fresh --seed\n";
