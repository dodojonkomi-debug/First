import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Region, District, School } from '@/types'
import { regionsApi } from '@/api/regions'
import { districtsApi } from '@/api/districts'
import { schoolsApi } from '@/api/schools'

export const useAppStore = defineStore('app', () => {
  const regions = ref<Region[]>([])
  const districts = ref<District[]>([])
  const schools = ref<School[]>([])
  const loading = ref(false)
  const sidebarCollapsed = ref(false)

  async function loadRegions() {
    try {
      const res = await regionsApi.getAll()
      regions.value = res.data.data
    } catch (e) {
      console.error('Error loading regions:', e)
    }
  }

  async function loadDistricts(regionId?: number) {
    try {
      const res = await districtsApi.getAll(regionId)
      districts.value = res.data.data
    } catch (e) {
      console.error('Error loading districts:', e)
    }
  }

  async function loadSchools(params?: { district_id?: number; region_id?: number }) {
    try {
      const res = await schoolsApi.getAll(params)
      schools.value = res.data.data
    } catch (e) {
      console.error('Error loading schools:', e)
    }
  }

  function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
  }

  return {
    regions, districts, schools, loading, sidebarCollapsed,
    loadRegions, loadDistricts, loadSchools, toggleSidebar,
  }
})
