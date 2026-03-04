<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Store;
use App\Models\Item;
use App\Models\Translation;

class CloneStore23WithItemsSeeder extends Seeder
{
    public function run(): void
    {
        $baseStoreId = 23;
        $storesToCreate = 5; // عدّل العدد براحتك

        $baseStore = Store::withoutGlobalScopes()
            ->with(['translations'])
            ->find($baseStoreId);

        if (!$baseStore) {
            throw new \Exception("Base store id={$baseStoreId} not found");
        }

        $baseItems = Item::withoutGlobalScopes()
            ->with(['translations'])
            ->where('store_id', $baseStoreId)
            ->get();

        DB::transaction(function () use ($baseStore, $baseItems, $storesToCreate) {

            for ($i = 1; $i <= $storesToCreate; $i++) {

                // 1) Clone Store
                $newStore = $baseStore->replicate();
                $newStore->name  = "{$baseStore->name} Copy {$i}";
                $newStore->email = $this->uniqueEmail($baseStore->email, $i);
                $newStore->phone = $this->uniquePhone($baseStore->phone, $i);

                if (isset($newStore->status)) $newStore->status = 1;
                if (isset($newStore->active)) $newStore->active = 1;

                $newStore->created_at = now();
                $newStore->updated_at = now();
                $newStore->save();

                // 2) Clone Store Translations
                Translation::where('translationable_type', Store::class)
                    ->where('translationable_id', $newStore->id)
                    ->delete();

                if ($baseStore->relationLoaded('translations') && $baseStore->translations) {
                    foreach ($baseStore->translations as $t) {
                        Translation::create([
                            'id' => $this->newTranslationId(),
                            'translationable_type' => Store::class,
                            'translationable_id' => $newStore->id,
                            'locale' => $t->locale,
                            'key' => $t->key,
                            'value' => $t->value,
                        ]);
                    }
                }

                // 3) Clone Store Schedule (اختياري)
                if (method_exists($newStore, 'schedules') && method_exists($baseStore, 'schedules')) {
                    try {
                        $baseSchedules = $baseStore->schedules()->get();
                        foreach ($baseSchedules as $sch) {
                            $newSch = $sch->replicate();
                            $newSch->store_id = $newStore->id;
                            $newSch->created_at = now();
                            $newSch->updated_at = now();
                            $newSch->save();
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                // 4) Clone Items
                foreach ($baseItems as $baseItem) {
                    $newItem = $baseItem->replicate();
                    $newItem->store_id = $newStore->id;

                    if (isset($newItem->name)) {
                        $newItem->name = "{$baseItem->name} ({$i})";
                    }

                    // Copy image if exists
                    if (isset($newItem->image) && $newItem->image) {
                        $newItem->image = $this->copyImageIfExists(
                            $newItem->image,
                            "cloned/store{$newStore->id}/"
                        );
                    }

                    $newItem->created_at = now();
                    $newItem->updated_at = now();
                    $newItem->save();

                    // 5) Clone Item Translations
                    Translation::where('translationable_type', Item::class)
                        ->where('translationable_id', $newItem->id)
                        ->delete();

                    if ($baseItem->relationLoaded('translations') && $baseItem->translations) {
                        foreach ($baseItem->translations as $t) {
                            Translation::create([
                                'id' => $this->newTranslationId(),
                                'translationable_type' => Item::class,
                                'translationable_id' => $newItem->id,
                                'locale' => $t->locale,
                                'key' => $t->key,
                                'value' => $t->value,
                            ]);
                        }
                    }

                    // 6) Clone Tags Pivot (اختياري)
                    if (method_exists($baseItem, 'tags') && method_exists($newItem, 'tags')) {
                        try {
                            $tagIds = $baseItem->tags()->pluck('tags.id')->toArray();
                            if (!empty($tagIds)) {
                                $newItem->tags()->sync($tagIds);
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                }
            }
        });
    }

    private function newTranslationId(): string
    {
        // لأن translations.id عندك مش auto increment
        return (string) Str::uuid();
    }

    private function uniqueEmail(?string $email, int $i): string
    {
        $email = $email ?: "store@example.com";
        $parts = explode('@', $email);
        $local = $parts[0] ?? 'store';
        $domain = $parts[1] ?? 'example.com';
        return "{$local}.copy{$i}@{$domain}";
    }

    private function uniquePhone(?string $phone, int $i): string
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);
        if (!$phone) $phone = "01000000000";
        return substr($phone, 0, max(0, strlen($phone) - 1)) . ($i % 10);
    }

    private function copyImageIfExists(string $imagePathOrName, string $targetDir): string
    {
        $candidates = [
            $imagePathOrName,
            "item/{$imagePathOrName}",
            "items/{$imagePathOrName}",
            "product/{$imagePathOrName}",
            "products/{$imagePathOrName}",
        ];

        $disk = Storage::disk('public');

        $found = null;
        foreach ($candidates as $p) {
            if ($disk->exists($p)) {
                $found = $p;
                break;
            }
        }

        if (!$found) {
            // لو الصور عندك مش على public disk (S3/مسار مختلف) سيب الاسم زي ما هو
            return $imagePathOrName;
        }

        if (!$disk->exists($targetDir)) {
            $disk->makeDirectory($targetDir);
        }

        $ext = pathinfo($found, PATHINFO_EXTENSION);
        $newName = $targetDir . Str::random(20) . ($ext ? ".{$ext}" : "");

        $disk->copy($found, $newName);

        return $newName;
    }
}
