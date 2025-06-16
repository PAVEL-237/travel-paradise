import { createSlice, PayloadAction } from '@reduxjs/toolkit'

interface Visitor {
  id: number
  firstName: string
  lastName: string
  isPresent: boolean
  comments?: string
  visitId: number
}

interface VisitorState {
  visitors: Visitor[]
  loading: boolean
  error: string | null
}

const initialState: VisitorState = {
  visitors: [],
  loading: false,
  error: null,
}

const visitorSlice = createSlice({
  name: 'visitors',
  initialState,
  reducers: {
    fetchVisitorsStart: (state) => {
      state.loading = true
      state.error = null
    },
    fetchVisitorsSuccess: (state, action: PayloadAction<Visitor[]>) => {
      state.loading = false
      state.visitors = action.payload
    },
    fetchVisitorsFailure: (state, action: PayloadAction<string>) => {
      state.loading = false
      state.error = action.payload
    },
    addVisitor: (state, action: PayloadAction<Visitor>) => {
      state.visitors.push(action.payload)
    },
    updateVisitor: (state, action: PayloadAction<Visitor>) => {
      const index = state.visitors.findIndex(visitor => visitor.id === action.payload.id)
      if (index !== -1) {
        state.visitors[index] = action.payload
      }
    },
    deleteVisitor: (state, action: PayloadAction<number>) => {
      state.visitors = state.visitors.filter(visitor => visitor.id !== action.payload)
    },
    updateVisitorPresence: (state, action: PayloadAction<{ id: number; isPresent: boolean }>) => {
      const visitor = state.visitors.find(v => v.id === action.payload.id)
      if (visitor) {
        visitor.isPresent = action.payload.isPresent
      }
    },
    updateVisitorComments: (state, action: PayloadAction<{ id: number; comments: string }>) => {
      const visitor = state.visitors.find(v => v.id === action.payload.id)
      if (visitor) {
        visitor.comments = action.payload.comments
      }
    },
  },
})

export const {
  fetchVisitorsStart,
  fetchVisitorsSuccess,
  fetchVisitorsFailure,
  addVisitor,
  updateVisitor,
  deleteVisitor,
  updateVisitorPresence,
  updateVisitorComments,
} = visitorSlice.actions

export default visitorSlice.reducer 