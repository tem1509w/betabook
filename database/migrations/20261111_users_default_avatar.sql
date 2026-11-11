UPDATE users
SET avatar_image = 'pfp-icon-set/Avatar-Pyrois.webp'
WHERE avatar_image IS NULL
   OR avatar_image NOT IN (
       'pfp-icon-set/Avatar_Aria.webp',
       'pfp-icon-set/Avatar_Dialyn.webp',
       'pfp-icon-set/Avatar_Ellen_Joe.webp',
       'pfp-icon-set/Avatar_Hoshimi_Miyabi.webp',
       'pfp-icon-set/Avatar_Lucia_Elowen.webp',
       'pfp-icon-set/Avatar_Nangong_Yu.webp',
       'pfp-icon-set/Avatar_Pyrois.webp',
       'pfp-icon-set/Avatar_Remielle_Dan.webp',
       'pfp-icon-set/Avatar_Roxy_Ifrita_Pryce.webp',
       'pfp-icon-set/Avatar_Sunna.webp',
       'pfp-icon-set/Avatar_Vivian_Banshee.webp',
       'pfp-icon-set/Avatar_Ye_Shunguang.webp',
       'pfp-icon-set/Avatar_Yixuan.webp'
   );

ALTER TABLE users
    MODIFY COLUMN avatar_image VARCHAR(255) NOT NULL
        DEFAULT 'pfp-icon-set/Avatar-Pyrois.webp';
