# ND3 – Dinaminis tinklapis

Studentas: Aivaras Guntulis  
Kursas: Interneto technologijos, VGTU  
Darbas: ND3 (Antrasis laboratorinis darbas)

---

##  Kaip paleisti projektą (Docker instrukcija)

### 1. Būtinos sąlygos
- Įdiegtas [Docker](https://www.docker.com/)
- Įdiegtas [Docker Compose](https://docs.docker.com/compose/install/)

### 2. Projekto paleidimas

Atidarykite terminalą projekto šakniniame kataloge (ten, kur yra failas `docker-compose.yaml`) ir vykdykite komandą:

```bash
docker-compose up --build
```

### 3. Aplikacijos pasiekiamumas

- Node.js aplikacija pasiekiama adresu: [http://localhost:5000](http://localhost:5000)
- Frontend puslapiai atidaromi tiesiogiai naršyklėje iš failų sistemos arba per web serverį (pvz., VSCode Live Server).

### 4. Projekto sustabdymas

Projektui sustabdyti naudokite komandą:

```bash
docker-compose down
```

---

