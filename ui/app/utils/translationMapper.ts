// utils/translationMapper.ts
import type { YiiTranslationItem, Segment, YiiTranslationResponse } from '~/types/translation'

export const TranslationMapper = {
  // Из API во Vue (Incoming)
  toFrontend(raw: any) {
    return {
      id: raw.sentenceId,           // Yii2 прислал sentenceId
      source_text: raw.origin,      // Yii2 прислал origin
      target_text: raw.translation, // Yii2 прислал translation
      author: raw.authorTranslate,  // Тот самый "Ai"
      is_verified: raw.isVerified,
      memory_id: raw.translationMemoryId
    }
  },

  // Из Vue в API (Outgoing для сохранения)
  toBackend(segment: Segment) {
    return {
      TranslationUpdate: {
        translation: segment.translate,
        // Сюда можно добавить статус и прочее
      }
    }
  }
}
