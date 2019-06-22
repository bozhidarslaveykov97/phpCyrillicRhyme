<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Word;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{

	public function search()
	{
		ini_set('memory_limit', '-1');
		
		$rhymeClassation = array();
		$getWord = Input::get('word');
		$getWordCombinations = $this->wordCombinations($getWord);

		$dbWords = Cache::rememberForever('words', function () {
			return Word::all();
		});
		
		foreach ($dbWords as $word) {
			
			$dbWordCombinations = $this->wordCombinations($word->word, 3);
			
			$wordCombination = mb_strtolower(end($getWordCombinations));
			$dbWordCombination = mb_strtolower(end($dbWordCombinations));
			
			if ($wordCombination == $dbWordCombination) {
				$rhymeClassation[] = array(
					'word'=>$word->word,
					'level'=>1
				);
			}
			
			/* foreach ($getWordCombinations as $wordCombination) {
				foreach($dbWordCombinations as $dbWordCombination) {
					
					$wordCombination = mb_strtolower($wordCombination);
					$dbWordCombination = mb_strtolower($dbWordCombination);
					
					if ($wordCombination == $dbWordCombination) {
						$rhymeClassation[] = array(
							'word'=>$word->word,
							'combinations'=>$dbWordCombinations,
							'level'=>1
						);
					}
				}
			} */
		}
		
		return view('welcome', ['results'=>$rhymeClassation]);
	}
	
	
	private function wordCombinations($word, $combinationNumbers = 3) {
		
		$combinations = array();
		
		$alphabets = $this->split($word);
		$i=0;
		foreach ($alphabets as $alpha) {
			$i++;
			
			if (!isset($alphabets[$i])) {
				continue;
			}
			
			if ($combinationNumbers > 2) {
				if (!isset($alphabets[$i+1])) {
					continue;
				}
			}
			
			if ($combinationNumbers > 3) {
				if (!isset($alphabets[$i+2])) {
					continue;
				}
			}
			
			if ($combinationNumbers > 4) {
				if (!isset($alphabets[$i+3])) {
					continue;
				}
			}
			
			$readyAlpha = $alpha . $alphabets[$i];
			
			if ($combinationNumbers > 2) {
				$readyAlpha .= $alphabets[$i+1];
			}
			
			if ($combinationNumbers > 3) {
				$readyAlpha .= $alphabets[$i+2];
			}
			
			if ($combinationNumbers > 4) {
				$readyAlpha .= $alphabets[$i+3];
			}
			
			$combinations[] = $readyAlpha;
		}
		
		return $combinations;
	}

	private function split($str, $len = 1)
	{
		$arr = [];
		$length = mb_strlen($str, 'UTF-8');

		for ($i = 0; $i < $length; $i += $len) {

			$arr[] = mb_substr($str, $i, $len, 'UTF-8');
		}

		return $arr;
	}
}
