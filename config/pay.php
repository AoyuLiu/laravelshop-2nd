<?php

return [
    'alipay' => [
        'app_id'         => '2016091800537235',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAm0umsZjbEIKb/WAHsnnE/Lm4G1S50IiHGJ6kRLeY84NkXovuogARNPpXqrcOjDgRz374XLAOB8y3ORX5pbYH1Mhq7+obGlZIPkwMZSk8BKhzESo2YXcSFL6Oi3d4RwkSXCoeidWULaky7q7P2xwvkcz4SP5AxK/SHadTEmfh0WOHGsBTx31VJb5TvpRowDlQnLOqdRxsKib932VaioTj4M8iuT2MvEQd5jCKrwANGQMd4gCWACWr8yxmeVR+CdPrj3rWVWkcSZRuDebXRrBxk+NmBbEfLzwQRW47UNjQ2anba4f1IVE1tzxyFotxTK9wC6XBT5vGIcG3iGPGYwp/iwIDAQAB',
        'private_key'    => 'MIIEpAIBAAKCAQEAq9a14Dhjipdn2l/S6Gh2LiHH0ukdc/2+SgbDY9B4gStaLN+HgLpsEKaASy/D34jbYplUm5OyiJNuOtfSfeGG3yWjdzmDekgKw2Jz+n80T415+Xlrk/4mj3SEZoBEw40pkbNlPho/HmZOEDc3CUmgECYoKo+1Ob/3hy6BfRCaNSsCbymoNCltsGByabPhF+uaVqaXZo8FgRL9ijLLUXmz93sSpjVdJFBIC9KuTnQR6yOU4ruyWmhUNIc5N3OHbHF74r/5WCJXuvP+E0bEfl6jOkPBY5jX3QVFY/zX6fazcLODRVsvAE/C0kYotVlisCCLuohv7WClc7YYHM/Z+hlWsQIDAQABAoIBAQCQRgUN7Eom4lffkPSTDKGsuc/71J1V9YT+wg7qju2eBbgAVOx/uAVjNWghuEjMNSt87R+MP8V1xdKWtP2VkvSaBg/o2fD5ieL/iHm3p3VUFYSDYN5hstDNQRaouKkDRlHQcr5eFctIyrxgAPvEYHxzEpZGpcGSawp4BSPSzU2rtMBnul9rV4xxfGnbmSm3z4Ivz55KtDOdsJtfo9XyIQXcmHowO9bj7+86on8JBHzm1XX75ihqW0qsuYYqDoT4fS9vE8nqggdseDlAwwMQF6Sxmw2unotjJcDRdvPcRTBlopgrt+bfMdWOaRyTobIfd3+TXHW8lKNQmf0NTi+pkmXBAoGBANVRjmYHc5sFzSaTUlDnSDlYnW9Tbs6nivdx1YQZK+NomHQVSxGVYShnDZx58puhnPta+HUDIUIQuprjuemTjEoGLhalX9OPYXca4BCh3VTVp+J/DooB765RTCPupcGgEHEPYAIBY+RiMD/ma7eATB2UQvG5vAD52opS8G8IksM/AoGBAM44gPKlWHBJ6SXC3Rv7900LpmIXDz/sMjn6l4n3D9fdR040W6JuXzdENni6Nj/3tHg/zADkn1Vlgq+UnXuSdvyBXZYuf5Xk2xRwfZzFOt0NSqjg6QUeq7e3/GJORmO7jnYeFeNEVWcu9OGaTqM3YMEsngjXgyUVmWMswGPo9JoPAoGBAIt8puaknL2Tz5AX6U2pzpphDaFMJzrOZ5piP0H/y6kKLhZKDipTSZLHwCi/vRpzVVkvJSQuhcLTeZHqsxi/OI7295ArVzvZl9vwIO//R+E/TGZYusMXfQi5dFZSqOSxq86iRR9KlW0zn2VJYMo/BIaa+iNiVkM1HegxT7LrIjy1AoGAUvUwCmyxkxpojCRDxooqBfh1ymf0Xoap/eDtLTcSTIhbsv4lDsbPzu/F6fMe98Sx+N3RN1rBLh1T4UYrxBY7f4CbIVs7QBV8fFB0d6hv8ZTzP9SaaNDZy2JcST+r2VsOCD46F97ZbFTbdhYZJFeWuJ82Q+BOhZCWz+qkyoxgOckCgYAe8k+v9yV9DUqshvzN29609qN/Cc1nGV5lZOrHD6dDm6SKkrN08J2i2IFru+f50ekxRYuebHCXRFB4hWgeS7+9WO0XeRmw3xK0jrPpuQoDYtFukU7IiIHO7Nt1dulNiDCOnVC8R6AlJjoSgvXzRl1P5+S1d1ZDHz5Kpww4ZsEbDA==',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
