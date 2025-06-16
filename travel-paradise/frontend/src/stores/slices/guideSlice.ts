import { createSlice, PayloadAction } from '@reduxjs/toolkit'

interface Guide {
  id: number
  firstName: string
  lastName: string
  photo: string
  status: 'ACTIVE' | 'INACTIVE'
  country: string
}

interface GuideState {
  guides: Guide[]
  selectedGuide: Guide | null
  loading: boolean
  error: string | null
}

const initialState: GuideState = {
  guides: [],
  selectedGuide: null,
  loading: false,
  error: null,
}

const guideSlice = createSlice({
  name: 'guides',
  initialState,
  reducers: {
    fetchGuidesStart: (state) => {
      state.loading = true
      state.error = null
    },
    fetchGuidesSuccess: (state, action: PayloadAction<Guide[]>) => {
      state.loading = false
      state.guides = action.payload
    },
    fetchGuidesFailure: (state, action: PayloadAction<string>) => {
      state.loading = false
      state.error = action.payload
    },
    selectGuide: (state, action: PayloadAction<Guide>) => {
      state.selectedGuide = action.payload
    },
    addGuide: (state, action: PayloadAction<Guide>) => {
      state.guides.push(action.payload)
    },
    updateGuide: (state, action: PayloadAction<Guide>) => {
      const index = state.guides.findIndex(guide => guide.id === action.payload.id)
      if (index !== -1) {
        state.guides[index] = action.payload
      }
    },
    deleteGuide: (state, action: PayloadAction<number>) => {
      state.guides = state.guides.filter(guide => guide.id !== action.payload)
    },
  },
})

export const {
  fetchGuidesStart,
  fetchGuidesSuccess,
  fetchGuidesFailure,
  selectGuide,
  addGuide,
  updateGuide,
  deleteGuide,
} = guideSlice.actions

export default guideSlice.reducer 