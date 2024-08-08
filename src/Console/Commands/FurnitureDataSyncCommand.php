<?php

namespace Atom\Core\Console\Commands;

use Atom\Core\Models\FurnitureData;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

use function Laravel\Prompts\progress;

class FurnitureDataSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atom:sync-furniture-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command for syncing the furniture data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $furnitureData = json_decode(file_get_contents(config('nitro.furniture_data_file')), true);

        progress(
            label: 'Syncing Room Items',
            steps: Arr::get($furnitureData, 'roomitemtypes.furnitype'),
            callback: fn (array $item) => $this->sync($item, 'roomitemtypes'),
        );

        progress(
            label: 'Syncing Wall Items',
            steps: Arr::get($furnitureData, 'wallitemtypes.furnitype'),
            callback: fn (array $item) => $this->sync($item, 'wallitemtypes'),
        );
    }

    /**
     * Sync the local furniture data.
     */
    public function sync(array $item, string $type): bool
    {
        return (bool) FurnitureData::withoutEvents(fn () => FurnitureData::updateOrCreate(['id' => Arr::get($item, 'id')], [
            'type' => $type,
            'classname' => Arr::get($item, 'classname'),
            'name' => Arr::get($item, 'name'),
            'revision' => Arr::get($item, 'revision'),
            'description' => Arr::get($item, 'description'),
            'category' => Arr::get($item, 'category'),
            'offerid' => Arr::get($item, 'offerid'),
            'defaultdir' => Arr::get($item, 'defaultdir', 0),
            'xdim' => Arr::get($item, 'xdim', 1),
            'ydim' => Arr::get($item, 'ydim', 1),
            'partcolors' => Arr::get($item, 'partcolors', []),
            'adurl' => Arr::get($item, 'adurl'),
            'buyout' => Arr::get($item, 'buyout'),
            'rentofferid' => Arr::get($item, 'rentofferid'),
            'rentbuyout' => Arr::get($item, 'rentbuyout'),
            'bc' => Arr::get($item, 'bc'),
            'excludeddynamic' => Arr::get($item, 'excludeddynamic'),
            'customparams' => Arr::get($item, 'customparams'),
            'specialtype' => Arr::get($item, 'specialtype'),
            'canstandon' => Arr::get($item, 'canstandon', 0),
            'cansiton' => Arr::get($item, 'cansiton', 0),
            'canlayon' => Arr::get($item, 'canlayon', 0),
            'furniline' => Arr::get($item, 'furniline'),
            'environment' => Arr::get($item, 'environment'),
            'rare' => Arr::get($item, 'rare'),
        ]));
    }
}