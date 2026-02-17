<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Privacy Policy - English
        Policy::create([
            'type' => 'privacy',
            'language' => 'en',
            'title' => 'Privacy Policy',
            'content' => $this->getPrivacyPolicyEn(),
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Privacy Policy - Azerbaijani
        Policy::create([
            'type' => 'privacy',
            'language' => 'az',
            'title' => 'Məxfilik Siyasəti',
            'content' => $this->getPrivacyPolicyAz(),
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Terms of Service - English
        Policy::create([
            'type' => 'terms',
            'language' => 'en',
            'title' => 'Terms of Service',
            'content' => $this->getTermsOfServiceEn(),
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Terms of Service - Azerbaijani
        Policy::create([
            'type' => 'terms',
            'language' => 'az',
            'title' => 'İstifadə Şərtləri',
            'content' => $this->getTermsOfServiceAz(),
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Data Processing Agreement - English
        Policy::create([
            'type' => 'data_processing',
            'language' => 'en',
            'title' => 'Data Processing Agreement',
            'content' => $this->getDataProcessingEn(),
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Data Processing Agreement - Azerbaijani
        Policy::create([
            'type' => 'data_processing',
            'language' => 'az',
            'title' => 'Məlumat Emalı Müqaviləsi',
            'content' => $this->getDataProcessingAz(),
            'is_active' => true,
            'effective_date' => now(),
        ]);
    }

    private function getPrivacyPolicyEn(): string
    {
        return '<h1>Privacy Policy</h1>
<p><strong>Last updated:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Information We Collect</h2>
<p>We collect information you provide directly to us, such as when you:</p>
<ul>
    <li>Create an account</li>
    <li>Submit race results</li>
    <li>Upload photos</li>
    <li>Contact us</li>
</ul>

<h2>2. How We Use Your Information</h2>
<p>We use the information we collect to:</p>
<ul>
    <li>Provide, maintain, and improve our services</li>
    <li>Process your race results and rankings</li>
    <li>Send you notifications and updates</li>
    <li>Respond to your comments and questions</li>
</ul>

<h2>3. Information Sharing</h2>
<p>We may share your information with:</p>
<ul>
    <li>Other athletes in the form of public race results and rankings</li>
    <li>Third-party service providers who assist us in operating our platform</li>
    <li>Law enforcement when required by law</li>
</ul>

<h2>4. Data Security</h2>
<p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>5. Your Rights</h2>
<p>You have the right to:</p>
<ul>
    <li>Access your personal information</li>
    <li>Correct inaccurate information</li>
    <li>Delete your account and personal information</li>
    <li>Object to processing of your information</li>
</ul>

<h2>6. Contact Us</h2>
<p>If you have any questions about this Privacy Policy, please contact us at support@ironman-app.com</p>';
    }

    private function getPrivacyPolicyAz(): string
    {
        return '<h1>Məxfilik Siyasəti</h1>
<p><strong>Son yenilənmə:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Topladığımız Məlumatlar</h2>
<p>Biz sizin birbaşa bizə təqdim etdiyiniz məlumatları toplayırıq, məsələn:</p>
<ul>
    <li>Hesab yaratdığınız zaman</li>
    <li>Yarış nəticələrini təqdim etdiyiniz zaman</li>
    <li>Fotoşəkil yükləyəndə</li>
    <li>Bizimlə əlaqə saxladığınız zaman</li>
</ul>

<h2>2. Məlumatlarınızı Necə İstifadə Edirik</h2>
<p>Topladığımız məlumatları aşağıdakılar üçün istifadə edirik:</p>
<ul>
    <li>Xidmətlərimizi təqdim etmək, saxlamaq və təkmilləşdirmək</li>
    <li>Yarış nəticələrinizi və reytinqlərinizi emal etmək</li>
    <li>Sizə bildirişlər və yeniliklər göndərmək</li>
    <li>Şərh və suallarınıza cavab vermək</li>
</ul>

<h2>3. Məlumatların Paylaşılması</h2>
<p>Məlumatlarınızı aşağıdakılarla paylaşa bilərik:</p>
<ul>
    <li>Digər atletlərlə ictimai yarış nəticələri və reytinqlər şəklində</li>
    <li>Platformamızın işləməsində bizə kömək edən üçüncü tərəf xidmət təminatçıları ilə</li>
    <li>Qanunla tələb olunduqda hüquq-mühafizə orqanları ilə</li>
</ul>

<h2>4. Məlumat Təhlükəsizliyi</h2>
<p>Şəxsi məlumatlarınızı icazəsiz giriş, dəyişiklik, açıqlama və ya məhv etməyə qarşı qorumaq üçün müvafiq texniki və təşkilati tədbirlər həyata keçiririk.</p>

<h2>5. Hüquqlarınız</h2>
<p>Sizin hüququnuz var:</p>
<ul>
    <li>Şəxsi məlumatlarınıza daxil olmaq</li>
    <li>Yanlış məlumatları düzəltmək</li>
    <li>Hesabınızı və şəxsi məlumatlarınızı silmek</li>
    <li>Məlumatlarınızın emalına etiraz etmək</li>
</ul>

<h2>6. Bizimlə Əlaqə</h2>
<p>Bu Məxfilik Siyasəti haqqında suallarınız varsa, lütfən support@ironman-app.com ünvanına müraciət edin</p>';
    }

    private function getTermsOfServiceEn(): string
    {
        return '<h1>Terms of Service</h1>
<p><strong>Last updated:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using this application, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>2. Use License</h2>
<p>Permission is granted to temporarily download one copy of the application for personal, non-commercial transitory viewing only.</p>

<h2>3. Disclaimer</h2>
<p>The materials in this application are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties.</p>

<h2>4. Limitations</h2>
<p>In no event shall our company or its suppliers be liable for any damages arising out of the use or inability to use the materials in this application.</p>

<h2>5. User Content</h2>
<p>You retain ownership of content you submit to the application. By submitting content, you grant us a worldwide, royalty-free license to use, reproduce, and distribute such content.</p>

<h2>6. Prohibited Uses</h2>
<p>You may not use our application:</p>
<ul>
    <li>For any unlawful purpose or to solicit others to commit unlawful acts</li>
    <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
    <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
    <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
</ul>

<h2>7. Contact Information</h2>
<p>For any questions about these Terms of Service, please contact us at support@ironman-app.com</p>';
    }

    private function getTermsOfServiceAz(): string
    {
        return '<h1>İstifadə Şərtləri</h1>
<p><strong>Son yenilənmə:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Şərtlərin Qəbulu</h2>
<p>Bu tətbiqi istifadə etməklə siz bu müqavilənin şərt və müddəaları ilə razılaşır və onlara əməl etməyi öhdənizə götürürsünüz.</p>

<h2>2. İstifadə Lisenziyası</h2>
<p>Tətbiqin bir nüsxəsini şəxsi, qeyri-kommersiya məqsədi ilə müvəqqəti olaraq yükləmək və görmək icazəsi verilir.</p>

<h2>3. İmtina</h2>
<p>Bu tətbiqdəki materiallar "olduğu kimi" təməlində təqdim edilir. Biz heç bir açıq və ya gizli zəmanət vermirik və digər bütün zəmanətlərdən imtina edirik.</p>

<h2>4. Məhdudiyyətlər</h2>
<p>Şirkətimiz və ya təchizatçılarımız bu tətbiqdəki materialların istifadəsi və ya istifadə edə bilməməsi nəticəsində yaranan zərərlərə görə məsuliyyət daşımırlar.</p>

<h2>5. İstifadəçi Məzmunu</h2>
<p>Tətbiqə göndərdiyiniz məzmunun sahibliyi sizə aiddir. Məzmun göndərməklə bizə həmin məzmunu istifadə, çoxaltmaq və yaymaq üçün dünya üzrə, royaltisiz lisenziya verirsiniz.</p>

<h2>6. Qadağan Edilmiş İstifadələr</h2>
<p>Tətbiqimizi aşağıdakılar üçün istifadə edə bilməzsiniz:</p>
<ul>
    <li>Hər hansı qeyri-qanuni məqsəd üçün və ya digərlərini qeyri-qanuni hərəkətlər etməyə sövq etmək</li>
    <li>Beynəlxalq, federal, əyalət və ya yerli qanun, qayda və qaydaları pozdurmaq</li>
    <li>Bizim və ya başqalarının əqli mülkiyyət hüquqlarını pozdurmaq</li>
    <li>Təcavüz, təhqir, aşağılama, zərər vermək, böhtan, şərəfə xələl gətirmək, intimidasiya və ya ayrı-seçkilik etmək</li>
</ul>

<h2>7. Əlaqə Məlumatları</h2>
<p>Bu İstifadə Şərtləri haqqında suallarınız üçün support@ironman-app.com ünvanına müraciət edin</p>';
    }

    private function getDataProcessingEn(): string
    {
        return '<h1>Data Processing Agreement</h1>
<p><strong>Last updated:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Purpose and Scope</h2>
<p>This Data Processing Agreement governs the processing of personal data in connection with the Ironman application services.</p>

<h2>2. Categories of Data</h2>
<p>We process the following categories of personal data:</p>
<ul>
    <li>Identity data (name, date of birth, gender)</li>
    <li>Contact data (email address, phone number)</li>
    <li>Profile data (athlete information, race results, photos)</li>
    <li>Technical data (IP address, device information, usage data)</li>
</ul>

<h2>3. Processing Activities</h2>
<p>Personal data is processed for the following purposes:</p>
<ul>
    <li>User account management</li>
    <li>Race result tracking and rankings</li>
    <li>Communication and notifications</li>
    <li>Application improvement and analytics</li>
</ul>

<h2>4. Legal Basis</h2>
<p>Processing is based on:</p>
<ul>
    <li>Your consent for specific processing activities</li>
    <li>Contract performance for service provision</li>
    <li>Legitimate interests for application improvement</li>
    <li>Legal obligations where applicable</li>
</ul>

<h2>5. Data Retention</h2>
<p>Personal data is retained for as long as necessary to fulfill the purposes for which it was collected, or as required by law.</p>

<h2>6. Data Subject Rights</h2>
<p>Under applicable data protection laws, you have the right to:</p>
<ul>
    <li>Access your personal data</li>
    <li>Rectify inaccurate data</li>
    <li>Erase your data (right to be forgotten)</li>
    <li>Restrict processing</li>
    <li>Data portability</li>
    <li>Object to processing</li>
</ul>

<h2>7. Security Measures</h2>
<p>We implement appropriate technical and organizational security measures to protect personal data against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>8. Contact</h2>
<p>For data protection inquiries, contact us at privacy@ironman-app.com</p>';
    }

    private function getDataProcessingAz(): string
    {
        return '<h1>Məlumat Emalı Müqaviləsi</h1>
<p><strong>Son yenilənmə:</strong> ' . now()->format('F d, Y') . '</p>

<h2>1. Məqsəd və Əhatə Dairəsi</h2>
<p>Bu Məlumat Emalı Müqaviləsi Ironman tətbiqi xidmətləri ilə əlaqədar şəxsi məlumatların emalını tənzimləyir.</p>

<h2>2. Məlumat Kateqoriyaları</h2>
<p>Biz aşağıdakı şəxsi məlumat kateqoriyalarını emal edirik:</p>
<ul>
    <li>Şəxsiyyət məlumatları (ad, doğum tarixi, cins)</li>
    <li>Əlaqə məlumatları (email ünvanı, telefon nömrəsi)</li>
    <li>Profil məlumatları (atlet məlumatları, yarış nəticələri, fotolar)</li>
    <li>Texniki məlumatlar (IP ünvan, cihaz məlumatları, istifadə məlumatları)</li>
</ul>

<h2>3. Emal Fəaliyyətləri</h2>
<p>Şəxsi məlumatlar aşağıdakı məqsədlər üçün emal edilir:</p>
<ul>
    <li>İstifadəçi hesabının idarə edilməsi</li>
    <li>Yarış nəticələrinin izlənməsi və reytinqlər</li>
    <li>Ünsiyyət və bildirişlər</li>
    <li>Tətbiqin təkmilləşdirilməsi və analitika</li>
</ul>

<h2>4. Hüquqi Əsas</h2>
<p>Emal aşağıdakılara əsaslanır:</p>
<ul>
    <li>Xüsusi emal fəaliyyətləri üçün sizin razılığınız</li>
    <li>Xidmət təminatı üçün müqavilə icrasıq</li>
    <li>Tətbiqin təkmilləşdirilməsi üçün qanuni maraqlar</li>
    <li>Tətbiq ediləcəyisə qanuni öhdəliklər</li>
</ul>

<h2>5. Məlumat Saxlama</h2>
<p>Şəxsi məlumatlar toplandığı məqsədləri yerinə yetirmək və ya qanunla tələb olunduqca lazım olduğu müddətdə saxlanılır.</p>

<h2>6. Məlumat Subyektinin Hüquqları</h2>
<p>Tətbiq edilən məlumat mühafizəsi qanunları əsasında sizin hüququnuz var:</p>
<ul>
    <li>Şəxsi məlumatlarınıza daxil olmaq</li>
    <li>Yanlış məlumatları düzəltmək</li>
    <li>Məlumatlarınızı silmək (unudulmaq hüququ)</li>
    <li>Emalı məhdudlaşdırmaq</li>
    <li>Məlumat portabilliyi</li>
    <li>Emala etiraz etmək</li>
</ul>

<h2>7. Təhlükəsizlik Tədbirləri</h2>
<p>Şəxsi məlumatları icazəsiz giriş, dəyişiklik, açıqlama və ya məhvdən qorumaq üçün müvafiq texniki və təşkilati təhlükəsizlik tədbirləri həyata keçiririk.</p>

<h2>8. Əlaqə</h2>
<p>Məlumat mühafizəsi ilə bağlı sorğular üçün privacy@ironman-app.com ünvanına müraciət edin</p>';
    }
}
