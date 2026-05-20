// types/translation.ts

// То, что прилетает из недр Yii2 (Raw Data)
export interface YiiTranslationItem {
  sentenceId: number
  origin: string
  translation: string
  isVerified: boolean
  authorTranslate?: string
  translationMemoryId: number
  // Добавь остальные поля, если нужно
}

export interface YiiTranslationResponse {
  translationList: YiiTranslationItem[]
  page: number
  pageCount: number
  // ... другие метаданные
}

// То, с чем работает наш фасад (Clean UI Model)
export interface Segment {
  id: number
  segment_number: number
  original: string
  translate: string
  state: 'confirmed' | 'new' | 'edited'
  translate_author: 'ai' | 'man' | 'user'
}
