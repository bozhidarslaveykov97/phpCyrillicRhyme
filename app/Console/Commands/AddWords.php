<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Word;

class AddWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'words:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add words in wordlist';

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
    {
    	$wordList = $this->_getWordlistFromFile();
    	
    	$databaseWords = array();
    	foreach(Word::all() as $word) {
    		$databaseWords[] = $word->word;
    	}
    	
    	foreach($wordList as $word) {
    		
    		$searchWord = array_search($word, $databaseWords);
    		
    		if (is_null($searchWord)) {
    			
    			$wordModel = new Word();
    			$wordModel->word = $word;
    			$wordModel->save();
    			
    			echo $word . PHP_EOL;
    		}
    		
    		
    	}
    	
    }
    
    private function _getWordlistFromFile()
    {
    	$words = array();
    	$wordList = file_get_contents(storage_path('wordlist.txt'));
    	
    	foreach(explode(PHP_EOL, $wordList) as $word) {
    		
    		$word = trim($word);
    		
    		if (empty($word)) {
    			continue;
    		}
    		
    		$words[] = $word;
    	}
    	
    	return $words;
    }
}
