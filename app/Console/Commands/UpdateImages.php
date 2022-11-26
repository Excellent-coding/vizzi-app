<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

class UpdateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:update-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create thumbnail versions of images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {/*
        $items = User::whereNotNull('photo')->get();
        echo "USERS: " . count($items);
        foreach ($items as $item) {
            $this->processImage('individuals', $item->id);
        }
        $items = Company::whereNotNull('photo')->get();
        echo "Company: " . count($items);
        foreach ($items as $item) {
            $this->processImage('companies', $item->id);
        }
        */
        foreach ($items as $item) {
                echo $item->id . "\n";
            $this->processImage('vessels', $item->id);
        }
    }

    public function processImage($folder, $id) {
        if (Storage::disk('aws')->exists('pictures/' . $folder . '/' . $id . '/cover.png' ) && 
            !Storage::disk('aws')->exists('pictures/' . $folder . '/' . $id . '/cover_rect.png' )) {        
            $file = Storage::disk('aws')->get('pictures/' . $folder . '/' . $id . '/cover.png');
            $imageSquare = \Image::make( $file );
            $imageSquare->fit(360, 290, function ($constraint) {
                    $constraint->upsize();
                }, 'bottom');
            $imageRect = \Image::make($file  );
            $imageRect->fit(472, 265, function ($constraint) {
                    $constraint->upsize();
                }, 'bottom');
            if (Storage::disk('aws')->put('pictures/' . $folder . '/' . $id . '/cover_square.png', (string)$imageSquare->encode('jpg'), 'public') &&
                Storage::disk('aws')->put('pictures/' . $folder . '/' . $id . '/cover_rect.png', (string)$imageRect->encode('jpg'), 'public'))     {
                return true;
            }
        }
        return false;
    }
}