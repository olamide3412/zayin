// resources/js/stores/themeStore.js
import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    theme: 'system', // Default theme is system
  }),
  getters: {
    isDark(state) {
      if (state.theme === 'system') {
        if (typeof window !== 'undefined') {
          return window.matchMedia('(prefers-color-scheme: dark)').matches
        }
        return false
      }
      return state.theme === 'dark'
    }
  },
  actions: {
    setTheme(newTheme) {
      this.theme = newTheme
      localStorage.setItem('theme', newTheme)
      this.applyTheme()
    },
    applyTheme() {
      const root = document.documentElement
      const systemIsDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      const themeToApply = this.theme === 'system' ? (systemIsDark ? 'dark' : 'light') : this.theme

      if (themeToApply === 'dark') {
        root.classList.add('dark')
      } else {
        root.classList.remove('dark')
      }
      root.setAttribute('data-theme', themeToApply)
    },
    loadTheme() {
      const saved = localStorage.getItem('theme')
      if (saved) {
        this.theme = saved
      } else {
        this.theme = 'system'
      }
      this.applyTheme()
    },
  },
})

