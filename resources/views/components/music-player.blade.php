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
        STORAGE_KEY: 'musicPlayerState',

        // Save current state to localStorage so it survives page navigation
        saveState: function() {
            if (this.songs.length === 0) return;
            const audioPlayer = document.getElementById('audioPlayer');
            const state = {
                songs: this.songs,
                currentSongIndex: this.currentSongIndex,
                currentTime: audioPlayer.currentTime,
                volume: audioPlayer.volume,
                wasPlaying: !audioPlayer.paused,
            };
            try {
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(state));
            } catch(e) {}
        },

        // Load state from localStorage
        loadState: function() {
            try {
                const raw = localStorage.getItem(this.STORAGE_KEY);
                return raw ? JSON.parse(raw) : null;
            } catch(e) {
                return null;
            }
        },

        init: function(songs = []) {
            this.setupEventListeners();

            if (songs.length > 0) {
                // Fresh songs provided (we're on a songs page) — use them
                this.songs = songs;
                this.currentSongIndex = 0;

                // Check if we were already playing one of these songs
                const saved = this.loadState();
                if (saved && saved.songs && saved.currentSongIndex < songs.length) {
                    const savedSong = saved.songs[saved.currentSongIndex];
                    const newSong = songs[saved.currentSongIndex];
                    // If same song, restore position
                    if (savedSong && newSong && savedSong.stream_url === newSong.stream_url) {
                        this.currentSongIndex = saved.currentSongIndex;
                        this.loadSong(this.currentSongIndex, false, saved.currentTime);
                        if (saved.wasPlaying) this.play();
                        this.bindSongItems();
                        return;
                    }
                }

                this.loadSong(0, false);
                this.bindSongItems();

            } else {
                // No songs passed — we're on a non-songs page, restore from localStorage
                const saved = this.loadState();
                if (saved && saved.songs && saved.songs.length > 0) {
                    this.songs = saved.songs;
                    this.currentSongIndex = saved.currentSongIndex || 0;
                    const audioPlayer = document.getElementById('audioPlayer');
                    if (saved.volume !== undefined) audioPlayer.volume = saved.volume;
                    this.loadSong(this.currentSongIndex, false, saved.currentTime);
                    if (saved.wasPlaying) this.play();
                }
            }
        },

        formatTime: function(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        play: function() {
            document.getElementById('audioPlayer').play();
        },

        pause: function() {
            document.getElementById('audioPlayer').pause();
        },

        loadSong: function(index, autoplay = false, resumeAt = 0) {
            if (this.songs.length === 0) return;
            if (index < 0) index = this.songs.length - 1;
            if (index >= this.songs.length) index = 0;

            this.currentSongIndex = index;
            const song = this.songs[index];
            const audioPlayer = document.getElementById('audioPlayer');

            audioPlayer.src = song.stream_url;

            // Restore position once metadata is ready
            if (resumeAt > 0) {
                audioPlayer.addEventListener('loadedmetadata', function onMeta() {
                    audioPlayer.currentTime = resumeAt;
                    audioPlayer.removeEventListener('loadedmetadata', onMeta);
                });
            }

            document.getElementById('playerTitle').textContent = song.name;
            document.getElementById('playerArtist').textContent = song.artist_name || 'Unknown Artist';
            document.getElementById('playerCover').src = song.album?.cover || song.cover || '{{ asset("image/default_artist.png") }}';

            // Reset progress UI
            document.getElementById('progress').style.width = resumeAt > 0 ? '' : '0%';
            document.getElementById('progressInput').value = 0;
            document.getElementById('currentTime').textContent = '0:00';

            // Highlight active song in list
            document.querySelectorAll('.song-item').forEach((item, i) => {
                item.classList.toggle('active', i === index);
            });

            this.saveState();

            if (autoplay) this.play();
        },

        bindSongItems: function() {
            document.querySelectorAll('.song-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    if (index < this.songs.length) {
                        this.loadSong(index, true);
                    }
                });
            });
        },

        setupEventListeners: function() {
            const audioPlayer = document.getElementById('audioPlayer');
            const playBtn = document.getElementById('playBtn');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const progressInput = document.getElementById('progressInput');
            const currentTimeEl = document.getElementById('currentTime');
            const durationEl = document.getElementById('duration');
            const volumeInput = document.getElementById('volumeInput');

            playBtn.addEventListener('click', () => {
                if (audioPlayer.paused) this.play();
                else this.pause();
            });

            nextBtn.addEventListener('click', () => this.loadSong(this.currentSongIndex + 1, true));
            prevBtn.addEventListener('click', () => this.loadSong(this.currentSongIndex - 1, true));

            // Icon sync
            audioPlayer.addEventListener('play', () => {
                playBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            audioPlayer.addEventListener('pause', () => {
                playBtn.innerHTML = '<i class="fas fa-play"></i>';
                this.saveState(); // save when paused so position is remembered
            });

            // Progress
            audioPlayer.addEventListener('timeupdate', () => {
                if (!this.isUserSeeking && audioPlayer.duration) {
                    const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                    progressInput.value = percent || 0;
                    document.getElementById('progress').style.width = percent + '%';
                    currentTimeEl.textContent = this.formatTime(audioPlayer.currentTime);
                }
                // Save position every ~5 seconds
                if (Math.floor(audioPlayer.currentTime) % 5 === 0) {
                    this.saveState();
                }
            });

            audioPlayer.addEventListener('loadedmetadata', () => {
                durationEl.textContent = this.formatTime(audioPlayer.duration);
            });

            // Seeking
            progressInput.addEventListener('mousedown', () => { this.isUserSeeking = true; });
            progressInput.addEventListener('mouseup', (e) => {
                this.isUserSeeking = false;
                audioPlayer.currentTime = (e.target.value / 100) * audioPlayer.duration;
                this.saveState();
            });
            progressInput.addEventListener('touchend', (e) => {
                this.isUserSeeking = false;
                audioPlayer.currentTime = (e.target.value / 100) * audioPlayer.duration;
                this.saveState();
            });

            // Volume
            volumeInput.addEventListener('input', (e) => {
                audioPlayer.volume = e.target.value / 100;
                this.saveState();
            });
            audioPlayer.volume = 0.7;

            // Auto-advance
            audioPlayer.addEventListener('ended', () => {
                this.loadSong(this.currentSongIndex + 1, true);
            });

            // Save state just before the page unloads
            window.addEventListener('beforeunload', () => {
                this.saveState();
            });
        }
    };
</script>