<?php

namespace App\Console\Commands;

use App\Mail\BrokerInquiryMail;
use App\Mail\TestEmail;
use App\Models\Appointment;
use Carbon\Traits\Date;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\NoReturn;

class TestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->{$this->argument('name')}();
    }

    protected function time_zone()
    {
        $appointment = Appointment::first();
        // 在控制器或模型中，格式化时间为应用的时区
        return $appointment->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    protected function aaa($words, $maxWidth)
    {
        $result = [];


        $wordsTwo = [];  // words 分割成二维数组
        $tmpLen = 0;   // 记录处理行的长度变量，如果 > $maxWidth 就 = 0
        $tmpArr = [];  // 记录处理行的内容变量，如果 $tmpLen > $maxWidth 就 = []
        for ($i = 0; $i < count($words); $i++) {
            $tmpArr[] = $words[$i];  // 先将 $i 放入 $tmpArr

            // 临时记录长度 + 当前字符长度 + 单词间的最少一个空格(第一个单词不加空格)
            $tmpLen += strlen($words[$i]) + (count($tmpArr) > 1 ? 1 : 0);

            // 大于$maxWidth。 踢出当前最后一个单词，$i回退一格，保存当前行，清空当前行
            if ($tmpLen > $maxWidth) {
                array_pop($tmpArr);
                $wordsTwo[] = $tmpArr;
                $tmpLen = 0;
                $tmpArr = [];
                $i--;
            }
        }
        // 最后次处理是否存在内容
        if (count($tmpArr)) $wordsTwo[] = $tmpArr;

        // 补充空格
        $wordsTwoLen = count($wordsTwo);
        for ($i = 0; $i < $wordsTwoLen; $i++) {

            // 要补的全部空格长度
            $spaceLen = $maxWidth - strlen(implode('', $wordsTwo[$i]));

            // 当前行长度
            $iArrLen = count($wordsTwo[$i]);

            // 当行只包含一个单词为左对齐
            if ($iArrLen == 1) {
                $result[] = $wordsTwo[$i][0] . $this->makeSpace($spaceLen);
                continue;
            }
            // 最后一行应为左对齐，且单词之间不插入额外的空格
            if (($wordsTwoLen - 1) == $i) {
                $lastStr = implode(' ', $wordsTwo[$i]);
                $result[] = $lastStr . $this->makeSpace($maxWidth - strlen($lastStr));
                continue;
            }

            $makeSpaceLen = $spaceLen / ($iArrLen - 1);
            // 如果可以整除，均铺
            if ($makeSpaceLen == intval($makeSpaceLen)) {
                $spaceStr = $this->makeSpace($makeSpaceLen);
                $result[] = implode($spaceStr, $wordsTwo[$i]);
                continue;
            }

            $rowStr = '';
            // 未整除  则左侧放置的空格数要多于右侧的空格数
            $iArrLenTmp = $iArrLen;
            $makeSpaceLenCeil = ceil($makeSpaceLen);  //先得到多的空格
            for ($j = 0; $j < $iArrLen; $j++) {

                $rowStr .= $wordsTwo[$i][$j];

                // 剩余拼接的长度小于
                if ($spaceLen < $makeSpaceLenCeil) continue;

                // 先拼上最大的 ，剩下的再去分
                $rowStr .= $this->makeSpace($makeSpaceLenCeil);
                // 拼接空格数量更新 总长缩减
                $spaceLen = $spaceLen - $makeSpaceLenCeil;
                // 去掉当前单词，剩下的单词再去分
                $iArrLenTmp--;
                if ($iArrLenTmp == 1) {
                    $makeSpaceLenCeil = 1;
                } else {
                    $makeSpaceLen = $spaceLen / ($iArrLenTmp - 1);
                    $makeSpaceLenCeil = ceil($makeSpaceLen);
                }
            }
            $result[] = $rowStr;
        }

        return $result;
    }

    protected function fulljustify(): void
    {
        $words = ["This", "is", "an", "example", "of", "text", "justification."];
        $maxWidth = 16;

        $wordsTwo = [];
        $tempLen = 0;
        $tempArr = [];
        for ($i = 0; $i < count($words); $i++) {
            $tempArr[] = $words[$i];
            $tempLen += strlen($words[$i]) + (count($tempArr) > 1 ? 1 : 0);

            if ($tempLen > $maxWidth) {
                array_pop($tempArr);
                $wordsTwo[] = $tempArr;
                $tempLen = 0;
                $tempArr = [];
                $i--;
            }
        }
        if (count($wordsTwo)) $wordsTwo[] = $tempArr;
        $wordsTwoLen = count($wordsTwo);
        for ($i = 0; $i < $wordsTwoLen; $i++) {
            // 要补的全部空格长度
            $spaceLen = $maxWidth - strlen(implode('', $wordsTwo[$i]));
            // 当前行长度
            $iArrLen = count($wordsTwo[$i]);
            // 当行只包含一个单词为左对齐
            if ($iArrLen == 1) {
                $result[] = $wordsTwo[$i][0] . $this->makeSpace($spaceLen);
                continue;
            }
            // 最后一行应为左对齐，且单词之间不插入额外的空格
            if (($wordsTwoLen - 1) == $i) {
                $lastStr = implode(' ', $wordsTwo[$i]);
                $result[] = $lastStr . $this->makeSpace($maxWidth - strlen($lastStr));
                continue;
            }

            $makeSpaceLen = $spaceLen / ($iArrLen - 1);
            // 如果可以整除，均铺
            if ($makeSpaceLen == intval($makeSpaceLen)) {
                $spaceStr = $this->makeSpace($makeSpaceLen);
                $result[] = implode($spaceStr, $wordsTwo[$i]);
                continue;
            }

            $rowStr = '';
            // 未整除  则左侧放置的空格数要多于右侧的空格数
            $iArrLenTmp = $iArrLen;
            $makeSpaceLenCeil = ceil($makeSpaceLen);  //先得到多的空格
            for ($j = 0; $j < $iArrLen; $j++) {

                $rowStr .= $wordsTwo[$i][$j];

                // 剩余拼接的长度小于
                if ($spaceLen < $makeSpaceLenCeil) continue;

                // 先拼上最大的 ，剩下的再去分
                $rowStr .= $this->makeSpace($makeSpaceLenCeil);
                // 拼接空格数量更新 总长缩减
                $spaceLen = $spaceLen - $makeSpaceLenCeil;
                // 去掉当前单词，剩下的单词再去分
                $iArrLenTmp--;
                if ($iArrLenTmp == 1) {
                    $makeSpaceLenCeil = 1;
                } else {
                    $makeSpaceLen = $spaceLen / ($iArrLenTmp - 1);
                    $makeSpaceLenCeil = ceil($makeSpaceLen);
                }
            }
            $result[] = $rowStr;
        }
        dd($result);
    }

    // 生成空格
    function makeSpace($len, $i = 0, $res = '')
    {
        while ($i++ < $len) {
            $res .= ' ';
        }
        return $res;
    }

    protected function testemail()
    {
        Mail::to('websupport@del-ev.com')->send(new TestEmail());
        dd('Test email sent!');
    }

    /**
     * 邮件格式验证
     */
    protected function validateemail()
    {
        //$email = "websupport@del-ev.com";
        $email = "sus@evolutionelectricvehicle.com";
        $isMatched = preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email, $matches);
        if ($isMatched) {
            $this->info('有效的电子邮件地址:' . $email);
        } else {
            $this->error('无效的电子邮件地址：' . $email);
        }
    }

    protected function group_anagrams()
    {
        $signature = 'app:test group_anagrams';
        $strs = ["eat", "tea", "tan", "ate", "nat", "bat"];
        $map = [];
        foreach ($strs as $str) {
            $arr = str_split($str);
            sort($arr);
            $temp = implode('', $arr);
            $map[$temp][] = $str;
        }
        dd(array_values($map));
    }

    protected function longestConsecutive()
    {
        $signature = 'app:test longestConsecutive';
        $nums = [9, 1, 4, 7, 3, -1, 0, 5, 8, -1, 6];
        if (empty($nums)) return 0;
        $set = array_flip($nums);
        $longest = 0;
        foreach ($nums as $num) {
            if (!isset($set[$num - 1])) {
                $currentNum = $num;
                $currentStreak = 1;

                while (isset($set[$currentNum + 1])) {
                    $currentNum++;
                    $currentStreak++;
                }
                $longest = max($longest, $currentStreak);
            }

        }
        //return $longest;
        dd($longest);
    }

    protected function maxArea()
    {
        $signature = 'php artisan app:test maxArea';
        $height = [1, 8, 6, 2, 5, 4, 8, 3, 7];
        $left = 0;
        $right = count($height) - 1;
        $maxArea = 0;

        while ($left < $right) {
            // 最小的高 * 底边长x轴
            $currentArea = min($height[$left], $height[$right]) * ($right - $left);
            $maxArea = max($maxArea, $currentArea);

            if ($height[$left] < $height[$right]) {
                $left++;
            } else {
                $right--;
            }
        }
        dd($maxArea);
    }

    protected function threeSum()
    {
        $signature = 'php artisan app:test threeSum';
        $nums = [-1, 0, 1, 2, -1, -4];
        sort($nums);
        $result = [];
        $n = count($nums);

        for ($i = 0; $i < $n - 2; $i++) {
            if ($i > 0 && $nums[$i] == $nums[$i - 1]) {
                continue;
            }
            $left = $i + 1;
            $right = $n - 1;
            while ($left < $right) {
                $sum = $nums[$i] + $nums[$left] + $nums[$right];
                if ($sum == 0) {
                    $result[] = [$nums[$i], $nums[$left], $nums[$right]];

                    while ($left < $right && $nums[$left] == $nums[$left + 1]) {
                        $left++;
                    }
                    while ($left < $right && $nums[$right] == $nums[$right - 1]) {
                        $right--;
                    }
                    $left++;
                    $right--;
                } elseif ($sum < 0) {
                    $left++;
                } else {
                    $right--;
                }
            }
        }
        //return $result;
        dd($result);

    }

}
