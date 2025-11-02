<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ঐ কিরে মধু 😆</title>
  <style>
    body {
      background: linear-gradient(135deg, #ff9a9e, #fad0c4, #fbc2eb);
      background-size: 300% 300%;
      animation: bgmove 10s ease infinite;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
      font-family: 'Comic Sans MS', cursive, sans-serif;
    }

    h1 {
      text-align: center;
      color: #fff;
      font-size: 3rem;
      line-height: 1.5;
      text-shadow: 3px 3px 0 #ff006e, -3px -3px 0 #8338ec;
      animation: bounce 1.5s infinite, rainbow 2s linear infinite;
      cursor: pointer;
    }

    h1:hover {
      transform: scale(1.2) rotate(3deg);
      text-shadow: 5px 5px 10px #000;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }

    @keyframes rainbow {
      0% { color: #ff006e; }
      25% { color: #fb5607; }
      50% { color: #ffbe0b; }
      75% { color: #8338ec; }
      100% { color: #3a86ff; }
    }

    @keyframes bgmove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .emoji {
      position: absolute;
      font-size: 2rem;
      animation: float 5s linear infinite;
      opacity: 0.8;
    }

    @keyframes float {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }
      30% { opacity: 1; }
      100% {
        transform: translateY(-10vh) rotate(720deg);
        opacity: 0;
      }
    }
  </style>
</head>
<body>
  <h1>
    ঐ কিরে 😆<br>
    ঐ কিরে 😜<br>
    ঐ কিরে 😝<br>
    মধু মধু মধু 🍯
  </h1>

  <script>
    const emojis = ['😂', '🤣', '😜', '🤪', '😆', '🥳', '🍯', '💥', '🔥', '✨'];
    setInterval(() => {
      const e = document.createElement('div');
      e.className = 'emoji';
      e.textContent = emojis[Math.floor(Math.random() * emojis.length)];
      e.style.left = Math.random() * 100 + 'vw';
      e.style.animationDuration = (3 + Math.random() * 3) + 's';
      document.body.appendChild(e);
      setTimeout(() => e.remove(), 6000);
    }, 300);
  </script>
</body>
</html>
