const express = require('express');
const mongoose = require('mongoose');
require('dotenv').config();

const app = express();
app.use(express.urlencoded({ extended: true }));

const userRoutes = require('./routes/users');
app.use('/', userRoutes);

const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://localhost:27017/nd3projektas';

mongoose.connect(MONGODB_URI, { useNewUrlParser: true, useUnifiedTopology: true })
  .then(() => console.log('Prisijungta prie MongoDB'))
  .catch(err => console.error('MongoDB klaida:', err));

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Serveris paleistas ant ${PORT}`);
});
