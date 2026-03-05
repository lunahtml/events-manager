/**
 * Events Manager Frontend Scripts
 */
(function () {
    'use strict';

    console.log('Events Manager JS loaded');
    console.log('events_ajax object:', events_ajax);

    class EventsManager {
        constructor() {
            this.container = document.querySelector('.events-manager-container');
            if (!this.container) {
                console.log('No events container found');
                return;
            }

            console.log('Events container found:', this.container);

            this.eventsList = this.container.querySelector('.events-list');
            this.loadMoreBtn = this.container.querySelector('.events-load-more-btn');
            this.statusMessage = this.container.querySelector('.events-status-message');

            this.limit = parseInt(this.container.dataset.limit) || 3;
            this.category = this.container.dataset.category || '';
            console.log('Category from container:', this.category);
            this.offset = parseInt(this.container.dataset.offset) || 0;
            this.total = parseInt(this.container.dataset.total) || 0;

            console.log('Initial state:', {
                limit: this.limit,
                offset: this.offset,
                total: this.total,
                hasMoreBtn: !!this.loadMoreBtn
            });

            this.isLoading = false;
            this.hasMore = this.offset < this.total;

            this.init();
        }

        init() {
            if (this.loadMoreBtn) {
                console.log('Adding click listener to load more button');
                this.loadMoreBtn.addEventListener('click', (e) => this.loadMore(e));
            }
        }

        async loadMore(event) {
            if (event) event.preventDefault();
            console.log('Load more clicked');

            if (this.isLoading) {
                console.log('Already loading');
                return;
            }

            if (!this.hasMore) {
                console.log('No more events to load');
                return;
            }

            this.isLoading = true;
            this.setLoadingState(true);

            const formData = new URLSearchParams();
            formData.append('action', 'load_more_events');
            formData.append('nonce', events_ajax.nonce);
            formData.append('offset', this.offset);
            formData.append('limit', this.limit);
            if (this.category) {
                formData.append('category', this.category);
            }

            console.log('Sending AJAX request with data:', {
                action: 'load_more_events',
                nonce: events_ajax.nonce,
                offset: this.offset,
                limit: this.limit
            });

            try {
                const response = await fetch(events_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                });

                console.log('Response received:', response);

                const data = await response.json();
                console.log('Data parsed:', data);

                if (data.success) {
                    this.handleSuccess(data.data);
                } else {
                    console.error('Server returned error:', data.data);
                    this.showMessage('Error loading events', 'error');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                this.showMessage('Network error', 'error');
            } finally {
                this.isLoading = false;
                this.setLoadingState(false);
            }
        }

        handleSuccess(data) {
            console.log('Handling success:', data);

            if (!data.html) {
                console.error('No HTML in response');
                return;
            }

            console.log('Appending HTML:', data.html);
            this.eventsList.insertAdjacentHTML('beforeend', data.html);

            this.offset += data.count;
            this.container.dataset.offset = this.offset;
            this.hasMore = this.offset < this.total;

            console.log('Updated state:', {
                newOffset: this.offset,
                hasMore: this.hasMore,
                total: this.total
            });

            if (!this.hasMore && this.loadMoreBtn) {
                console.log('No more events, hiding button');
                this.loadMoreBtn.style.display = 'none';
            }

            this.showMessage('Events loaded successfully!', 'success');
        }

        setLoadingState(loading) {
            if (!this.loadMoreBtn) return;

            if (loading) {
                this.loadMoreBtn.classList.add('loading');
                this.loadMoreBtn.disabled = true;
            } else {
                this.loadMoreBtn.classList.remove('loading');
                this.loadMoreBtn.disabled = false;
            }
        }

        showMessage(message, type = 'info') {
            console.log(`Message (${type}):`, message);
            if (!this.statusMessage) return;

            this.statusMessage.textContent = message;
            this.statusMessage.className = `events-status-message ${type}`;
            this.statusMessage.style.display = 'block';

            if (type === 'success') {
                setTimeout(() => {
                    this.statusMessage.style.display = 'none';
                }, 3000);
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new EventsManager();
        });
    } else {
        new EventsManager();
    }
})();