<!-- Music Player - Fixed at Bottom -->
<div class="music-player-wrapper">
    <div class="music-player">

        <!-- Left: Cover + Track Info -->
        <div class="player-track">
            <div class="player-cover-container">
                <img id="playerCover" src="{{ asset('image/default_artist.png') }}" alt="Album Cover" class="player-cover-small">
            </div>
            <div class="player-info">
                <h2 id="playerTitle" class="player-title">No Track</h2>
                <p id="playerArtist" class="player-artist">Select a song to play</p>
            </div>
        </div>

        <!-- Center: Controls + Progress Bar -->
        <div class="player-center">
            <div class="player-controls">
                <button id="prevBtn" class="control-btn">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button id="playBtn" class="control-btn play-btn">
                    <i class="fas fa-play"></i>
                </button>
                <button id="nextBtn" class="control-btn">
                    <i class="fas fa-step-forward"></i>
                </button>
            </div>
            <div class="progress-container">
                <span id="currentTime" class="time">0:00</span>
                <div class="progress-bar">
                    <div id="progress" class="progress"></div>
                    <input type="range" id="progressInput" min="0" max="100" value="0">
                </div>
                <span id="duration" class="time">0:00</span>
            </div>
        </div>

        <!-- Right: Volume Control -->
        <div class="volume-control">
            <i class="fas fa-volume-low"></i>
            <input type="range" id="volumeInput" min="0" max="100" value="70">
            <i class="fas fa-volume-high"></i>
        </div>

        <audio id="audioPlayer"></audio>
    </div>
</div>

<script>
    window.musicPlayer = {
        songs: [],
        currentSongIndex: 0,
        isUserSeeking: false,
        
        init: function(songs = []) {
            this.songs = songs;
            this.setupEventListeners();
            if (this.songs.length > 0) {
                this.loadSong(0);
            }
        },
        
        formatTime: function(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },
        
        loadSong: function(index) {
            if (this.songs.length === 0) return;
            if (index < 0) index = this.songs.length - 1;
            if (index >= this.songs.length) index = 0;
            
            this.currentSongIndex = index;
            const song = this.songs[index];
            const audioPlayer = document.getElementById('audioPlayer');
            
            audioPlayer.src = song.stream_url;
            document.getElementById('playerTitle').textContent = song.name;
            document.getElementById('playerArtist').textContent = song.artist_name || 'Unknown Artist';
            document.getElementById('playerCover').src = song.album?.cover || song.cover || '{{ asset("image/default_artist.png") }}';
            
            const songItems = document.querySelectorAll('.song-item');
            songItems.forEach((item, i) => {
                item.classList.toggle('active', i === index);
            });
        },
        
        setupEventListeners: function() {
            const audioPlayer = document.getElementById('audioPlayer');
            const playBtn = document.getElementById('playBtn');
            const coverPlayBtn = document.getElementById('coverPlayBtn');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const progressInput = document.getElementById('progressInput');
            const currentTimeEl = document.getElementById('currentTime');
            const durationEl = document.getElementById('duration');
            const volumeInput = document.getElementById('volumeInput');
            
            const togglePlay = () => {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                    playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    coverPlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    audioPlayer.pause();
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                    coverPlayBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            };
            
            playBtn.addEventListener('click', togglePlay);
            coverPlayBtn.addEventListener('click', togglePlay);
            
            nextBtn.addEventListener('click', () => {
                this.loadSong(this.currentSongIndex + 1);
                audioPlayer.play();
                playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                coverPlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            
            prevBtn.addEventListener('click', () => {
                this.loadSong(this.currentSongIndex - 1);
                audioPlayer.play();
                playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                coverPlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            
            audioPlayer.addEventListener('timeupdate', () => {
                if (!this.isUserSeeking && audioPlayer.duration) {
                    const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                    progressInput.value = percent || 0;
                    document.getElementById('progress').style.width = percent + '%';
                    currentTimeEl.textContent = this.formatTime(audioPlayer.currentTime);
                }
            });
            
            audioPlayer.addEventListener('loadedmetadata', () => {
                durationEl.textContent = this.formatTime(audioPlayer.duration);
            });
            
            progressInput.addEventListener('mousedown', () => { this.isUserSeeking = true; });
            progressInput.addEventListener('mouseup', (e) => {
                this.isUserSeeking = false;
                audioPlayer.currentTime = (e.target.value / 100) * audioPlayer.duration;
            });
            progressInput.addEventListener('touchend', (e) => {
                this.isUserSeeking = false;
                audioPlayer.currentTime = (e.target.value / 100) * audioPlayer.duration;
            });
            
            volumeInput.addEventListener('input', (e) => {
                audioPlayer.volume = e.target.value / 100;
            });
            
            audioPlayer.addEventListener('ended', () => { nextBtn.click(); });
            audioPlayer.addEventListener('play', () => {
                playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                coverPlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            audioPlayer.addEventListener('pause', () => {
                playBtn.innerHTML = '<i class="fas fa-play"></i>';
                coverPlayBtn.innerHTML = '<i class="fas fa-play"></i>';
            });
            
            audioPlayer.volume = 0.7;
        }
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        const songItems = document.querySelectorAll('.song-item');
        songItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                const songsData = window.musicPlayer.songs;
                if (index < songsData.length) {
                    window.musicPlayer.loadSong(index);
                    document.getElementById('audioPlayer').play();
                    document.getElementById('playBtn').innerHTML = '<i class="fas fa-pause"></i>';
                    document.getElementById('coverPlayBtn').innerHTML = '<i class="fas fa-pause"></i>';
                }
            });
        });
    });
</script>