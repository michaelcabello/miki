<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

//php artisan make:seeder CompanySeeder
class CompanySeeder extends Seeder
{

    public function run(): void
    {
        Company::create([
            'ruc' => '20447393302',
            'razonsocial' => 'TICOM',
            'ubigeo' => "150101",
            'direccion' => 'Av. Peru 1255',
            'currency_id' => 1,
            'soluser' => "MODDATOS",
            'solpass' => "MODDATOS",
            'ublversion' => "2.1",
            'detraccion' => 700,
            'certificate_path' => 'fe/certificados/dy2eLjhjZJvYb9P2XOM86KTqC8sfpoQIE9vmw4zb.txt',
            //'logo' => 'fe/logos/0jdmPxuXhJXZ4IFjjN11goSZYMg26HkpQ8zg2GOS.png',
            'logo' => '',
            'certificate_path' => "certificates/certificate_1.pem",
            'certificado' => "-----BEGIN PRIVATE KEY-----
            MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDUHbPExymJyOWE
            bxY4TlCufbh54zUAonBNU0hkfCsuLXOIz2KCmH6bTdO907Ms9dTqkcXMwxD87/o8
            qwyRaNiIGTsqrSSshMxkJy5t+folVXUrUNf3IDz1Y6zulyxzQdVFx1ThDIPITtF6
            wQ+paBhobLFFgkul4DtEA9dMAp7VBoGdUpdzuOIF7v/UyMUF7hsxRMAXAqnMBnlt
            bTytNN5j7HBq0sWNvduVn6xW2mimZIplUSWykvYbnfQv2HqS4DX/f18+5ULLFmzB
            Erhme9d6DM7SXY44GbT0AkA9du0dApoQFhAqNMKFZYvGa5axWYeLHfF32g81nG/I
            18yGrcfRAgMBAAECggEADoQd2lia4iAKfP6xMZdCdD6MUmMXLHzxXIlXifDpb5aS
            sokmv7M57tzrobEMMQZ91LO3KqUq03SE1oQKLyVStDWt0+TXfqrz5eK8jbAuy0FG
            7Hjy3qmpIk349rcHxrd5pfXXPDOEDUA/m7v8m2ZRTUwq8YvSK37l72in4j7HqeJR
            HvFbf/G1GqRpyKQO8aRenPmtE4gD9JVIRFIj6tNaSvBzRpnDTGDpYUriOISic3WJ
            YMHr2ANM2hKTz2YQG7JmRvwGR1zULTLNyoThLFtP6MTOgq04zfWB55ZLveO8JCww
            HxuK85+k5MQ4GSu1nUe+HLxS7uZ5625w7ZB+t42iAQKBgQD69+DV3xL1R7XYckQ6
            TZ6MG8aZYOPqIMEvLyThUGvc6afz3aPhn7494I+jmBqK9P3juVG0RG1MS84mwpG2
            mg9qwzY213OQvasdbi/KFN8kMdt7AYu5IJ0ISHCGv3fMXi3+mOD6MlG3BMsngNow
            6uml2H0z8Pv1FVqooygP0uNycQKBgQDYXmkcqWnCtnzwh1fCKTII0GkWCE8RJNXv
            6N52Im9T4hvys9LT00YCJ6ma2EWpSflpLkreDj71BVPVTh5TFUTk9M4xvjLDdcLQ
            E27oFWxIMmeX/2lZnoS6/d2tm++Zwi3rXA6N0zVENzFfruPVBeOAaFfP5L1KtKRj
            4uE1bAWbYQKBgQCHMZDEpW6pAwBKoQNwBPAruaq6ZR9huFNY/6R2W8Q/NP9stzDZ
            EhyBaL73+bASuvcp/WKuIU5fk1ZyOs4T99nmQVKrKFTw27uaFwlXavbpoJIDKUoD
            aDYviBZWAD6gsPtF80T+gqzSUpq9pQPk5icHWB/aIy8XT3GO9pVWMNylgQKBgCCi
            lN4i23XoCo5JC76YchiMPt144VwnnzExgaR16y7O0wJXhzw2CMA4dUeKyW8QXlM0
            DUzS/0H7zLpGryI++gZCunscQhHjSEAUPk05NfzpxWBSwPQoicKemfoepBQgCscO
            Oo+/xLAGVyckfO7blYX/twb/bGHBP25lgSyKn4nhAoGACvOKOL9vyziaLeISY85f
            jG1TfeCKG7zsbw95Oy4qfccpSUUgLFuR35A56G0OAc7GNXqrTZ6UjrAJltT5bG+J
            u5grbeJ4i1pF/6xoh/pjtfWLUUbsghgRqCqv0z5YemopXTEpVE/vPPC2JhZHfTVO
            nnk/MZT7Cwl87tYexykKDbc=
            -----END PRIVATE KEY-----
            -----BEGIN CERTIFICATE-----
            MIIFCDCCA/CgAwIBAgIJAMwye7towTY2MA0GCSqGSIb3DQEBCwUAMIIBDTEbMBkG
            CgmSJomT8ixkARkWC0xMQU1BLlBFIFNBMQswCQYDVQQGEwJQRTENMAsGA1UECAwE
            TElNQTENMAsGA1UEBwwETElNQTEYMBYGA1UECgwPVFUgRU1QUkVTQSBTLkEuMUUw
            QwYDVQQLDDxETkkgOTk5OTk5OSBSVUMgMjA2MDkyNzgyMzUgLSBDRVJUSUZJQ0FE
            TyBQQVJBIERFTU9TVFJBQ0nDk04xRDBCBgNVBAMMO05PTUJSRSBSRVBSRVNFTlRB
            TlRFIExFR0FMIC0gQ0VSVElGSUNBRE8gUEFSQSBERU1PU1RSQUNJw5NOMRwwGgYJ
            KoZIhvcNAQkBFg1kZW1vQGxsYW1hLnBlMB4XDTIzMDUyNTE0NDIyMVoXDTI1MDUy
            NDE0NDIyMVowggENMRswGQYKCZImiZPyLGQBGRYLTExBTUEuUEUgU0ExCzAJBgNV
            BAYTAlBFMQ0wCwYDVQQIDARMSU1BMQ0wCwYDVQQHDARMSU1BMRgwFgYDVQQKDA9U
            VSBFTVBSRVNBIFMuQS4xRTBDBgNVBAsMPEROSSA5OTk5OTk5IFJVQyAyMDYwOTI3
            ODIzNSAtIENFUlRJRklDQURPIFBBUkEgREVNT1NUUkFDScOTTjFEMEIGA1UEAww7
            Tk9NQlJFIFJFUFJFU0VOVEFOVEUgTEVHQUwgLSBDRVJUSUZJQ0FETyBQQVJBIERF
            TU9TVFJBQ0nDk04xHDAaBgkqhkiG9w0BCQEWDWRlbW9AbGxhbWEucGUwggEiMA0G
            CSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDUHbPExymJyOWEbxY4TlCufbh54zUA
            onBNU0hkfCsuLXOIz2KCmH6bTdO907Ms9dTqkcXMwxD87/o8qwyRaNiIGTsqrSSs
            hMxkJy5t+folVXUrUNf3IDz1Y6zulyxzQdVFx1ThDIPITtF6wQ+paBhobLFFgkul
            4DtEA9dMAp7VBoGdUpdzuOIF7v/UyMUF7hsxRMAXAqnMBnltbTytNN5j7HBq0sWN
            vduVn6xW2mimZIplUSWykvYbnfQv2HqS4DX/f18+5ULLFmzBErhme9d6DM7SXY44
            GbT0AkA9du0dApoQFhAqNMKFZYvGa5axWYeLHfF32g81nG/I18yGrcfRAgMBAAGj
            ZzBlMB0GA1UdDgQWBBShW9h2j1hnFWmHL+95E8qbgMHlwDAfBgNVHSMEGDAWgBSh
            W9h2j1hnFWmHL+95E8qbgMHlwDATBgNVHSUEDDAKBggrBgEFBQcDATAOBgNVHQ8B
            Af8EBAMCB4AwDQYJKoZIhvcNAQELBQADggEBABWmSUiUwKCR+E//0BBCngo3vT3b
            J13diCsoPOIcWzRQqE+qQ+pbBwXISke5w0gv6+gV/E/r8yiNqwuJnoM1/5jyFe4j
            mF2gIgL0kpiWtnkrT4qn24Y5t/FuQKJtbZx4ec0Uh6n7NzmUoTjm2tP42IqhLQSn
            GhWXXnXxh9XGjeVc7SdCIsyvAQ+CbTXJPvIfwTpTtg500NOQaGEIP3lBd5dNLcEp
            sErwCa4Ln2Hob2wSXeA3FX8qutkHhiVyGAxaLsy2aW5xVBeR4G24WAYtnjiARYTm
            Q03NoAh6oA46zA1LzaF+lpcIPbqNAdb4B4gJ0os+mCgwXx8DkEMSSZvWUMI=
            -----END CERTIFICATE-----
            ",
        ]);
    }
}
