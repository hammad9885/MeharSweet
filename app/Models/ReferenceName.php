<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceName extends Model
{
    use HasFactory;
    protected $table = 'reference_names';
    protected $fillable = ['name', 'name_urdu', 'name_en', 'cid'];

    public static function urduToEnglishTitle($text)
    {
        if (empty($text)) {
            return '';
        }

        $dictionary = [
            'شیراز جہانگیرہ' => 'Shiraz Jahangira',
            'عمرٹوائزرحیم یارخاں' => 'Umar Toys Rahim Yar Khan',
            'الحمدٹوائزملتان' => 'Alhamd Toys Multan',
            'حاجی ندیم چکوال' => 'Haji Nadeem Chakwal',
            'محمود ٹوائز اوکاڑہ' => 'Mahmood Toys Okara',
            'افضل دریا خان' => 'Afzal Darya Khan',
            'سلمان صوابی' => 'Salman Swabi',
            'مٹھو چکوال' => 'Mithu Chakwal',
            'اسامہ تلہ گنگ' => 'Usama Talagang',
            'برکت ڈیرہ غازی خان' => 'Barkat Dera Ghazi Khan',
            'اقبال خضرو' => 'Iqbal Hazro',
            'ابراہیم بٹ خیلہ' => 'Ibrahim Batkhela',
            'بلال ڈیرہ غازی خان' => 'Bilal Dera Ghazi Khan',
            'قریشی ڈیرہ غازی خان' => 'Qureshi Dera Ghazi Khan',
            'وسیم چوک اعظم' => 'Waseem Chowk Azam',
            'سعید ڈیرہ غازی خان' => 'Saeed Dera Ghazi Khan',
            'شاہ عالم چکوال' => 'Shah Alam Chakwal',
            'الیاس دریا خان' => 'Ilyas Darya Khan',
            'الکمال ٹوائزڈی-جی-خان' => 'Al Kamal Toys DG Khan',
            'مقصوداحمد ہڑپہ' => 'Maqsood Ahmed Harappa',
            'پیرنواب علی شاہ بنوں' => 'Pir Nawab Ali Shah Bannu',
            'نوید ٹوایز ہڑپہ' => 'Naveed Toys Harappa',
            'قدیر' => 'Qadeer',
            'بلال' => 'Bilal',
            'طارق' => 'Tariq',
            'قاسم' => 'Qasim',
            'علی حمزہ' => 'Ali Hamza',
            'خرم' => 'Khurram',
            'Self-خود' => 'Self',
            'منور(گوشی)' => 'Munawar (Goshi)',
            'ارسلان' => 'Arslan',
            'یوسف' => 'Yousaf',
            'عمر' => 'Umar',
            'عنصر' => 'Anser',
            'احسان ٹوائز لاڑکانہ' => 'Ahsan Toys Larkana',
        ];

        $clean = trim($text);
        if (isset($dictionary[$clean])) {
            return $dictionary[$clean];
        }

        $wordMap = [
            'شیراز' => 'Shiraz', 'جہانگیرہ' => 'Jahangira', 'عمر' => 'Umar',
            'ٹوائز' => 'Toys', 'ٹوایز' => 'Toys', 'رحیم' => 'Rahim', 'یار' => 'Yar',
            'خان' => 'Khan', 'خاں' => 'Khan', 'الحمد' => 'Alhamd', 'ملتان' => 'Multan',
            'حاجی' => 'Haji', 'ندیم' => 'Nadeem', 'چکوال' => 'Chakwal', 'محمود' => 'Mahmood',
            'اوکاڑہ' => 'Okara', 'افضل' => 'Afzal', 'دریا' => 'Darya', 'سلمان' => 'Salman',
            'صوابی' => 'Swabi', 'مٹھو' => 'Mithu', 'اسامہ' => 'Usama', 'تلہ' => 'Tala',
            'گنگ' => 'Gang', 'برکت' => 'Barkat', 'ڈیرہ' => 'Dera', 'غازی' => 'Ghazi',
            'اقبال' => 'Iqbal', 'خضرو' => 'Hazro', 'ابراہیم' => 'Ibrahim', 'بٹ' => 'Bat',
            'خیلہ' => 'Khela', 'بلال' => 'Bilal', 'قریشی' => 'Qureshi', 'وسیم' => 'Waseem',
            'چوک' => 'Chowk', 'اعظم' => 'Azam', 'سعید' => 'Saeed', 'شاہ' => 'Shah',
            'عالم' => 'Alam', 'الیاس' => 'Ilyas', 'الکمال' => 'Al-Kamal', 'مقصود' => 'Maqsood',
            'احمد' => 'Ahmed', 'ہڑپہ' => 'Harappa', 'پیر' => 'Pir', 'نواب' => 'Nawab',
            'علی' => 'Ali', 'بنوں' => 'Bannu', 'نوید' => 'Naveed', 'قدیر' => 'Qadeer',
            'طارق' => 'Tariq', 'قاسم' => 'Qasim', 'حمزہ' => 'Hamza', 'خرم' => 'Khurram',
            'خود' => 'Khud', 'منور' => 'Munawar', 'ارسلان' => 'Arslan', 'یوسف' => 'Yousaf',
            'عنصر' => 'Anser', 'احسان' => 'Ahsan', 'لاڑکانہ' => 'Larkana',
        ];

        $tokens = preg_split('/[\s\-_()]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $translated = [];
        foreach ($tokens as $token) {
            $translated[] = $wordMap[$token] ?? self::urduToRoman($token);
        }

        return trim(implode(' ', array_filter($translated)));
    }

    public static function urduToRoman($text)
    {
        if (empty($text)) {
            return '';
        }

        $dictionary = [
            'شیراز جہانگیرہ' => 'Shiraz Jahangira Jahangirah sheeraz',
            'عمرٹوائزرحیم یارخاں' => 'Umar Toys Rahim Yar Khan Rahimyar Khan omar omer',
            'الحمدٹوائزملتان' => 'Alhamd Toys Multan Al-Hamd',
            'حاجی ندیم چکوال' => 'Haji Nadeem Chakwal Nadim',
            'محمود ٹوائز اوکاڑہ' => 'Mahmood Toys Okara Mehmood mahmud',
            'افضل دریا خان' => 'Afzal Darya Khan Daryakhan',
            'سلمان صوابی' => 'Salman Swabi Swaby',
            'مٹھو چکوال' => 'Mithu Chakwal Mitthu Mithoo',
            'اسامہ تلہ گنگ' => 'Usama Talagang Osama Tallagang',
            'برکت ڈیرہ غازی خان' => 'Barkat Dera Ghazi Khan DG Khan dgkhan',
            'اقبال خضرو' => 'Iqbal Hazro Khazro',
            'ابراہیم بٹ خیلہ' => 'Ibrahim Batkhela Butkhela',
            'بلال ڈیرہ غازی خان' => 'Bilal Dera Ghazi Khan DG Khan dgkhan',
            'قریشی ڈیرہ غازی خان' => 'Qureshi Dera Ghazi Khan DG Khan Kureshi dgkhan',
            'وسیم چوک اعظم' => 'Waseem Chowk Azam Wasim Chouk',
            'سعید ڈیرہ غازی خان' => 'Saeed Dera Ghazi Khan DG Khan Said dgkhan',
            'شاہ عالم چکوال' => 'Shah Alam Chakwal',
            'الیاس دریا خان' => 'Ilyas Darya Khan Elias',
            'الکمال ٹوائزڈی-جی-خان' => 'Al Kamal Toys DG Khan Alkamal Dera Ghazi Khan dgkhan',
            'مقصوداحمد ہڑپہ' => 'Maqsood Ahmed Harappa Harapa',
            'پیرنواب علی شاہ بنوں' => 'Pir Nawab Ali Shah Bannu Peer Nawab',
            'نوید ٹوایز ہڑپہ' => 'Naveed Toys Harappa Navid',
            'قدیر' => 'Qadeer Kadeer Qadir Kadhir',
            'بلال' => 'Bilal Belaal Bilall',
            'طارق' => 'Tariq Tarik Tareeq Tareek',
            'قاسم' => 'Qasim Kasim Qasem',
            'علی حمزہ' => 'Ali Hamza Aly Humza',
            'خرم' => 'Khurram Khuram',
            'Self-خود' => 'Self Khud Khod',
            'منور(گوشی)' => 'Munawar Goshi Munawwar',
            'ارسلان' => 'Arslan Arsalan',
            'یوسف' => 'Yousaf Yousuf Yusuf Yosef',
            'عمر' => 'Umar Omer Omar',
            'عنصر' => 'Anser Ansar Ansur',
            'احسان ٹوائز لاڑکانہ' => 'Ahsan Toys Larkana Ehsan',
        ];

        $clean = trim($text);
        $roman = $dictionary[$clean] ?? '';

        $wordMap = [
            'شیراز' => 'shiraz sheeraz', 'جہانگیرہ' => 'jahangira jahangirah', 'عمر' => 'umar omer omar',
            'ٹوائز' => 'toys', 'ٹوایز' => 'toys', 'رحیم' => 'rahim', 'یار' => 'yar',
            'خان' => 'khan', 'خاں' => 'khan', 'الحمد' => 'alhamd al-hamd', 'ملتان' => 'multan',
            'حاجی' => 'haji', 'ندیم' => 'nadeem nadim', 'چکوال' => 'chakwal', 'محمود' => 'mahmood mehmood mahmud',
            'اوکاڑہ' => 'okara', 'افضل' => 'afzal', 'دریا' => 'darya', 'سلمان' => 'salman',
            'صوابی' => 'swabi swaby', 'مٹھو' => 'mithu mitthu mithoo', 'اسامہ' => 'usama osama', 'تلہ' => 'tala',
            'گنگ' => 'gang talagang tallagang', 'برکت' => 'barkat', 'ڈیرہ' => 'dera dg', 'غازی' => 'ghazi dg',
            'اقبال' => 'iqbal', 'خضرو' => 'hazro khazro', 'ابراہیم' => 'ibrahim', 'بٹ' => 'bat but',
            'خیلہ' => 'khela batkhela', 'بلال' => 'bilal', 'قریشی' => 'qureshi kureshi', 'وسیم' => 'waseem wasim',
            'چوک' => 'chowk chouk', 'اعظم' => 'azam', 'سعید' => 'saeed said', 'شاہ' => 'shah',
            'عالم' => 'alam', 'الیاس' => 'ilyas elias', 'الکمال' => 'alkamal al kamal', 'مقصود' => 'maqsood',
            'احمد' => 'ahmed ahmad', 'ہڑپہ' => 'harappa harapa', 'پیر' => 'pir peer', 'نواب' => 'nawab',
            'علی' => 'ali', 'بنوں' => 'bannu', 'نوید' => 'naveed navid', 'قدیر' => 'qadeer qadir kadeer',
            'طارق' => 'tariq tarik tareeq', 'قاسم' => 'qasim kasim', 'حمزہ' => 'hamza humza', 'خرم' => 'khurram khuram',
            'خود' => 'khud self', 'منور' => 'munawar munawwar', 'گوشی' => 'goshi', 'ارسلان' => 'arslan arsalan',
            'یوسف' => 'yousaf yousuf yusuf', 'عنصر' => 'anser ansar', 'احسان' => 'ahsan ehsan', 'لاڑکانہ' => 'larkana',
        ];

        $tokens = preg_split('/[\s\-_()]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $wordRomans = [];
        foreach ($tokens as $tok) {
            if (isset($wordMap[$tok])) {
                $wordRomans[] = $wordMap[$tok];
            }
        }

        $map = [
            'آ' => 'aa', 'ا' => 'a', 'ب' => 'b', 'پ' => 'p', 'ت' => 't', 'ٹ' => 't',
            'ث' => 's', 'ج' => 'j', 'چ' => 'ch', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
            'ڈ' => 'd', 'ذ' => 'z', 'ر' => 'r', 'ڑ' => 'r', 'ز' => 'z', 'ژ' => 'zh',
            'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'z', 'ط' => 't', 'ظ' => 'z',
            'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q', 'ک' => 'k', 'گ' => 'g',
            'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ں' => 'n', 'و' => 'o', 'ہ' => 'h',
            'ۂ' => 'h', 'ۃ' => 't', 'ھ' => 'h', 'ی' => 'i', 'ے' => 'e', 'ئ' => 'i',
        ];

        $phonetic = str_replace(array_keys($map), array_values($map), $clean);

        return trim(implode(' ', array_unique(array_filter(array_merge([$roman], $wordRomans, [$phonetic])))));
    }

    public function getEnNameAttribute()
    {
        if (!empty($this->name_en)) {
            return $this->name_en;
        }
        if (!empty($this->name_urdu) && !preg_match('/\p{Arabic}/u', $this->name_urdu)) {
            return $this->name_urdu;
        }
        if (!empty($this->name) && !preg_match('/\p{Arabic}/u', $this->name)) {
            return $this->name;
        }
        return self::urduToEnglishTitle($this->name ?: $this->name_urdu);
    }

    public function getSearchTermsAttribute()
    {
        $terms = [];
        if (!empty($this->name)) {
            $terms[] = $this->name;
            $terms[] = self::urduToRoman($this->name);
        }
        if (!empty($this->name_urdu)) {
            $terms[] = $this->name_urdu;
            $terms[] = self::urduToRoman($this->name_urdu);
        }
        if (!empty($this->name_en)) {
            $terms[] = $this->name_en;
        }
        if ($this->en_name) {
            $terms[] = $this->en_name;
        }
        if ($this->urdu_name) {
            $terms[] = $this->urdu_name;
            $terms[] = self::urduToRoman($this->urdu_name);
        }
        return trim(preg_replace('/\s+/', ' ', implode(' ', array_unique(array_filter($terms)))));
    }

    public function getUrduNameAttribute()
    {
        if (preg_match('/\p{Arabic}/u', $this->name)) {
            return $this->name;
        }
        if (!empty($this->name_urdu) && preg_match('/\p{Arabic}/u', $this->name_urdu)) {
            return $this->name_urdu;
        }
        $englishToUrdu = [
            'umar' => 'عمر',
            'umar -' => 'عمر',
            'self' => 'خود',
            'self-خود' => 'خود',
            'bilal' => 'بلال',
            'tariq' => 'طارق',
            'qasim' => 'قاسم',
            'khurram' => 'خرم',
            'arslan' => 'ارسلان',
            'yousaf' => 'یوسف',
            'anser' => 'عنصر',
        ];
        $key = strtolower(trim($this->name ?: $this->name_urdu));
        if (isset($englishToUrdu[$key])) {
            return $englishToUrdu[$key];
        }
        return $this->name ?: $this->name_urdu;
    }
}
