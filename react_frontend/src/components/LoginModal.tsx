import { useState } from 'react';
import { X, LogIn, UserPlus } from 'lucide-react';

interface LoginModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function LoginModal({ isOpen, onClose }: LoginModalProps) {
  const [mode, setMode] = useState<'choice' | 'signup' | 'login'>('choice');
  const [signupCode, setSignupCode] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  if (!isOpen) return null;

  const handleSignupSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Handle signup with code
    console.log('Signup with code:', signupCode);
  };

  const handleLoginSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Handle login
    console.log('Login:', { email, password });
  };

  const resetAndClose = () => {
    setMode('choice');
    setSignupCode('');
    setEmail('');
    setPassword('');
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl max-w-md w-full relative">
        <button
          onClick={resetAndClose}
          className="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
        >
          <X className="w-6 h-6" />
        </button>

        <div className="p-8">
          {/* Choice View */}
          {mode === 'choice' && (
            <div>
              <div className="text-center mb-8">
                <div className="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <span className="text-white">C</span>
                </div>
                <h2 className="text-gray-900 mb-2">
                  Welcome to CorsaireCRM
                </h2>
                <p className="text-gray-600">
                  Choose how you'd like to continue
                </p>
              </div>

              <div className="space-y-3">
                <button
                  onClick={() => setMode('signup')}
                  className="w-full px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-3"
                >
                  <UserPlus className="w-5 h-5" />
                  <span>Sign Up with Code</span>
                </button>
                
                <a
                  href="https://corsairetech.com?action=login"
                  className="w-full px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-gray-400 transition-colors flex items-center justify-center gap-3"
                >
                  <LogIn className="w-5 h-5" />
                  <span>Login as Existing Customer</span>
                </a>
              </div>

              <p className="text-gray-500 text-center mt-6">
                New to CorsaireCRM? Contact sales for a signup code.
              </p>
            </div>
          )}

          {/* Signup View */}
          {mode === 'signup' && (
            <div>
              <button
                onClick={() => setMode('choice')}
                className="text-gray-600 hover:text-gray-900 mb-6 flex items-center gap-2"
              >
                ← Back
              </button>
              
              <h2 className="text-gray-900 mb-2">
                Sign Up with Code
              </h2>
              <p className="text-gray-600 mb-6">
                Enter the signup code provided by your administrator
              </p>

              <form onSubmit={handleSignupSubmit} className="space-y-4">
                <div>
                  <label htmlFor="signupCode" className="block text-gray-700 mb-2">
                    Signup Code
                  </label>
                  <input
                    type="text"
                    id="signupCode"
                    value={signupCode}
                    onChange={(e) => setSignupCode(e.target.value)}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your signup code"
                    required
                  />
                </div>

                <button
                  type="submit"
                  className="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Continue
                </button>
              </form>

              <div className="mt-6 text-center">
                <p className="text-gray-600">
                  Don't have a code?{' '}
                  <a href="#" className="text-blue-600 hover:text-blue-700">
                    Contact sales
                  </a>
                </p>
              </div>
            </div>
          )}

          {/* Login View */}
          {mode === 'login' && (
            <div>
              <button
                onClick={() => setMode('choice')}
                className="text-gray-600 hover:text-gray-900 mb-6 flex items-center gap-2"
              >
                ← Back
              </button>
              
              <h2 className="text-gray-900 mb-2">
                Welcome Back
              </h2>
              <p className="text-gray-600 mb-6">
                Login to your CorsaireCRM account
              </p>

              <form onSubmit={handleLoginSubmit} className="space-y-4">
                <div>
                  <label htmlFor="email" className="block text-gray-700 mb-2">
                    Email Address
                  </label>
                  <input
                    type="email"
                    id="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="you@company.com"
                    required
                  />
                </div>

                <div>
                  <label htmlFor="password" className="block text-gray-700 mb-2">
                    Password
                  </label>
                  <input
                    type="password"
                    id="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your password"
                    required
                  />
                </div>

                <div className="flex items-center justify-between">
                  <label className="flex items-center gap-2">
                    <input type="checkbox" className="rounded" />
                    <span className="text-gray-700">Remember me</span>
                  </label>
                  <a href="#" className="text-blue-600 hover:text-blue-700">
                    Forgot password?
                  </a>
                </div>

                <button
                  type="submit"
                  className="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Login
                </button>
              </form>

              <div className="mt-6 text-center">
                <p className="text-gray-600">
                  New to CorsaireCRM?{' '}
                  <button
                    onClick={() => setMode('signup')}
                    className="text-blue-600 hover:text-blue-700"
                  >
                    Sign up with code
                  </button>
                </p>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}