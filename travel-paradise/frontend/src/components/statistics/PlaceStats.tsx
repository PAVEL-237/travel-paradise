import React from 'react';
import {
  Paper,
  Typography,
  Box,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Rating
} from '@mui/material';
import {
  PieChart,
  Pie,
  Cell,
  ResponsiveContainer,
  Tooltip
} from 'recharts';

interface PlaceStatsProps {
  byCategory: Array<{
    category: string;
    count: number;
  }>;
  mostVisited: Array<{
    name: string;
    visits: number;
  }>;
  bestRated: Array<{
    name: string;
    rating: number;
  }>;
  averageRating: number;
}

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8'];

const PlaceStats: React.FC<PlaceStatsProps> = ({
  byCategory,
  mostVisited,
  bestRated,
  averageRating
}) => {
  return (
    <Paper sx={{ p: 3, height: '100%' }}>
      <Typography variant="h6" gutterBottom>
        Place Statistics
      </Typography>

      <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        {/* Category Distribution */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Category Distribution
          </Typography>
          <Box sx={{ height: 200 }}>
            <ResponsiveContainer>
              <PieChart>
                <Pie
                  data={byCategory}
                  dataKey="count"
                  nameKey="category"
                  cx="50%"
                  cy="50%"
                  outerRadius={80}
                  label
                >
                  {byCategory.map((entry, index) => (
                    <Cell
                      key={`cell-${index}`}
                      fill={COLORS[index % COLORS.length]}
                    />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </Box>
        </Box>

        {/* Most Visited Places */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Most Visited Places
          </Typography>
          <TableContainer>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Place</TableCell>
                  <TableCell align="right">Visits</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {mostVisited.map((place) => (
                  <TableRow key={place.name}>
                    <TableCell>{place.name}</TableCell>
                    <TableCell align="right">{place.visits}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Box>

        {/* Best Rated Places */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Best Rated Places
          </Typography>
          <TableContainer>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Place</TableCell>
                  <TableCell align="right">Rating</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {bestRated.map((place) => (
                  <TableRow key={place.name}>
                    <TableCell>{place.name}</TableCell>
                    <TableCell align="right">
                      <Rating value={place.rating} readOnly precision={0.1} />
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Box>

        {/* Average Rating */}
        <Box>
          <Typography variant="subtitle1" gutterBottom>
            Average Rating
          </Typography>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
            <Rating value={averageRating} readOnly precision={0.1} />
            <Typography variant="h6">
              {averageRating.toFixed(1)}
            </Typography>
          </Box>
        </Box>
      </Box>
    </Paper>
  );
};

export default PlaceStats; 