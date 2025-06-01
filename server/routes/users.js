const express = require('express');
const router = express.Router();
const User = require('../models/User');

// HTML CRUD pagrindinis puslapis
router.get('/', async (req, res) => {
  const users = await User.find();
  let rows = users.map(user => `
    <tr>
      <td>${user.firstName}</td>
      <td>${user.lastName}</td>
      <td>${user.email}</td>
      <td>
        <form action="/edit/${user._id}" method="post" style="display:inline;">
          <input type="text" name="firstName" value="${user.firstName}" required style="width:80px;" />
          <input type="text" name="lastName" value="${user.lastName}" required style="width:80px;" />
          <input type="email" name="email" value="${user.email}" required style="width:140px;" />
          <button type="submit">💾</button>
        </form>
        <a href="/delete/${user._id}" onclick="return confirm('Ištrinti?');">🗑️</a>
      </td>
    </tr>
  `).join('');
  res.send(`
    <h1>Vartotojų sąrašas</h1>
    <form action="/add" method="post">
      <input type="text" name="firstName" placeholder="Vardas" required />
      <input type="text" name="lastName" placeholder="Pavardė" required />
      <input type="email" name="email" placeholder="El.paštas" required />
      <input type="text" name="password" placeholder="Slaptažodis" required />
      <button type="submit">Registruoti</button>
    </form>
    <table border="1" cellpadding="5" style="margin-top:15px;">
      <tr><th>Vardas</th><th>Pavardė</th><th>El.paštas</th><th>Veiksmai</th></tr>
      ${rows}
    </table>
  `);
});

// Sukurti naują vartotoją
router.post('/add', async (req, res) => {
  try {
    const { firstName, lastName, email, password } = req.body;
    const user = new User({ firstName, lastName, email, password });
    await user.save();
    res.redirect('/');
  } catch (err) {
    res.send('<h2>Klaida kuriant vartotoją (el.paštas turi būti unikalus).</h2><a href="/">Grįžti</a>');
  }
});

// Atnaujinti vartotoją
router.post('/edit/:id', async (req, res) => {
  try {
    const { firstName, lastName, email } = req.body;
    await User.findByIdAndUpdate(req.params.id, { firstName, lastName, email });
    res.redirect('/');
  } catch (err) {
    res.send('<h2>Klaida atnaujinant vartotoją.</h2><a href="/">Grįžti</a>');
  }
});

// Ištrinti vartotoją
router.get('/delete/:id', async (req, res) => {
  try {
    await User.findByIdAndDelete(req.params.id);
    res.redirect('/');
  } catch (err) {
    res.send('<h2>Klaida trinant vartotoją.</h2><a href="/">Grįžti</a>');
  }
});

module.exports = router;
