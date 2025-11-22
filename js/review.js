/**
 * Review Management JavaScript
 * Handles product review interactions
 */

/**
 * Load reviews for a product
 */
function loadReviews(productId) {
    fetch('../actions/review_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayReviews(data.reviews);
            updateReviewStats(data.stats);
        }
    })
    .catch(error => {
        console.error('Error loading reviews:', error);
    });
}

/**
 * Display reviews in the reviews list
 */
function displayReviews(reviews) {
    const container = document.getElementById('reviewsList');
    if (!container) return;
    
    if (!reviews || reviews.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                <i class="fas fa-comment-slash" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p style="font-size: 16px; margin: 0;">No reviews yet. Be the first to review this product!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = reviews.map(review => {
        const customerName = escapeHtml(review.customer_name);
        const initials = customerName.charAt(0).toUpperCase();
        const rating = parseInt(review.rating);
        const reviewText = escapeHtml(review.review_text || '');
        const reviewDate = new Date(review.review_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        const isVerified = review.is_verified_purchase == 1;
        
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                starsHtml += '<i class="fas fa-star" style="color: #fbbf24;"></i>';
            } else {
                starsHtml += '<i class="far fa-star" style="color: #d1d5db;"></i>';
            }
        }
        
        return `
            <div class="review-item" style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                ${initials}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">
                                    ${customerName}
                                    ${isVerified ? '<span style="display: inline-block; margin-left: 8px; padding: 2px 8px; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 11px; font-weight: 600;"><i class="fas fa-check-circle"></i> Verified Purchase</span>' : ''}
                                </div>
                                <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">
                                    ${starsHtml}
                                    <span style="margin-left: 8px;">${rating}.0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="color: #6b7280; font-size: 13px;">${reviewDate}</div>
                </div>
                ${reviewText ? `<p style="color: #374151; line-height: 1.6; margin: 0;">${nl2br(reviewText)}</p>` : ''}
            </div>
        `;
    }).join('');
}

/**
 * Update review statistics display
 */
function updateReviewStats(stats) {
    if (!stats) return;
    
    // Update average rating
    const avgRating = parseFloat(stats.avg_rating || 0);
    const totalReviews = parseInt(stats.total_reviews || 0);
    
    // This would update the stats section if needed
    // For now, the PHP renders it server-side
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Convert newlines to <br> tags
 */
function nl2br(text) {
    return text.replace(/\n/g, '<br>');
}

