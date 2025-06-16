import { createSlice, PayloadAction } from '@reduxjs/toolkit'

interface Visit {
  id: number
  photo: string
  country: string
  location: string
  date: string
  startTime: string
  duration: number
  endTime: string
  guideId: number
  status: 'SCHEDULED' | 'IN_PROGRESS' | 'COMPLETED' | 'CANCELLED'
  generalComment?: string
}

interface VisitState {
  visits: Visit[]
  selectedVisit: Visit | null
  loading: boolean
  error: string | null
}

const initialState: VisitState = {
  visits: [],
  selectedVisit: null,
  loading: false,
  error: null,
}

const visitSlice = createSlice({
  name: 'visits',
  initialState,
  reducers: {
    fetchVisitsStart: (state) => {
      state.loading = true
      state.error = null
    },
    fetchVisitsSuccess: (state, action: PayloadAction<Visit[]>) => {
      state.loading = false
      state.visits = action.payload
    },
    fetchVisitsFailure: (state, action: PayloadAction<string>) => {
      state.loading = false
      state.error = action.payload
    },
    selectVisit: (state, action: PayloadAction<Visit>) => {
      state.selectedVisit = action.payload
    },
    addVisit: (state, action: PayloadAction<Visit>) => {
      state.visits.push(action.payload)
    },
    updateVisit: (state, action: PayloadAction<Visit>) => {
      const index = state.visits.findIndex(visit => visit.id === action.payload.id)
      if (index !== -1) {
        state.visits[index] = action.payload
      }
    },
    deleteVisit: (state, action: PayloadAction<number>) => {
      state.visits = state.visits.filter(visit => visit.id !== action.payload)
    },
    updateVisitStatus: (state, action: PayloadAction<{ id: number; status: Visit['status'] }>) => {
      const visit = state.visits.find(v => v.id === action.payload.id)
      if (visit) {
        visit.status = action.payload.status
      }
    },
    updateVisitComment: (state, action: PayloadAction<{ id: number; comment: string }>) => {
      const visit = state.visits.find(v => v.id === action.payload.id)
      if (visit) {
        visit.generalComment = action.payload.comment
      }
    },
  },
})

export const {
  fetchVisitsStart,
  fetchVisitsSuccess,
  fetchVisitsFailure,
  selectVisit,
  addVisit,
  updateVisit,
  deleteVisit,
  updateVisitStatus,
  updateVisitComment,
} = visitSlice.actions

export default visitSlice.reducer 