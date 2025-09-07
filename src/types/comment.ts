export interface Comment {
  id: string;
  videoId: string;
  userId: string;
  userName: string;
  userRole: 'student' | 'admin' | 'instructor';
  content: string;
  timestamp: Date;
  parentId?: string; // For replies
  replies?: Comment[];
  likes: number;
  likedBy: string[];
  isEdited: boolean;
  editedAt?: Date;
}

export interface CommentFormData {
  content: string;
  parentId?: string;
}

export interface CommentStats {
  totalComments: number;
  totalReplies: number;
}

export interface CommentFilter {
  sortBy: 'newest' | 'oldest' | 'mostLiked';
  showReplies: boolean;
}