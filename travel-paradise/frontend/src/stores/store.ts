import { configureStore } from '@reduxjs/toolkit'
import authReducer from './slices/authSlice'
import guideReducer from './slices/guideSlice'
import visitReducer from './slices/visitSlice'
import visitorReducer from './slices/visitorSlice'

export const store = configureStore({
  reducer: {
    auth: authReducer,
    guides: guideReducer,
    visits: visitReducer,
    visitors: visitorReducer,
  },
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch 