DROP TABLE IF EXISTS translation_segments CASCADE;

-- Создаем тип для автора перевода
DO $$ BEGIN
    CREATE TYPE author_type AS ENUM ('ai','chat_gpt','yandex','google','deepseek','man','memory');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

CREATE TABLE translation_segments (
    sentence_id INT PRIMARY KEY,
    translation_segment_config TEXT,
    segment_config TEXT,
    user_id INT,
    origin_task_file_id INT,
    origin TEXT,
    translation TEXT,
    is_verified SMALLINT DEFAULT 0,
    is_bad_file SMALLINT DEFAULT 0,
    is_current SMALLINT DEFAULT 1,
    author_translate author_type, 
    translation_memory_id INT,
    comment TEXT,
    sort_order INT,
    page_number INT,
    is_in_translation_memory INT,
    translation_table_comment_id INT,
    char_count INT,
    created_at INT,
    updated_at INT
);

CREATE INDEX idx_seg_render ON translation_segments (origin_task_file_id, sort_order);

-- UPDATE translation_segments ts SET is_current = 1;

DROP TABLE IF EXISTS legacy_sync_status CASCADE;

CREATE TABLE legacy_sync_status (
    origin_task_file_id INT PRIMARY KEY,
    synced_pages TEXT,
    last_sync_at INT
);
